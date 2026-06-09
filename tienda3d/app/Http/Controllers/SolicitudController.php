<?php

namespace App\Http\Controllers;

use App\Models\SolicitudCotizacion;
use App\Models\SolicitudArchivo;
use App\Models\SolicitudItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    private const EXTENSIONES_ARCHIVO = ['stl', 'obj', '3mf', 'jpg', 'jpeg', 'png', 'pdf'];

    /**
     * finfo detecta los STL/OBJ/3MF como application/octet-stream o text/plain
     * (no tienen tipo MIME estándar reconocible), por lo que la regla `mimes`
     * basada en contenido siempre los rechaza. Validamos por extensión real.
     */
    private function reglaExtensionArchivo(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if (!in_array(strtolower($value->getClientOriginalExtension()), self::EXTENSIONES_ARCHIVO)) {
                $fail('El archivo debe ser de tipo: ' . implode(', ', self::EXTENSIONES_ARCHIVO) . '.');
            }
        };
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion'        => 'nullable|string|max:2000',
            'material_preferido' => 'nullable|string|max:100',
            'cantidad'           => 'nullable|integer|min:1',
            'uso_final'          => 'nullable|string|max:500',
            'archivos.*'         => ['nullable', 'file', 'max:20480', $this->reglaExtensionArchivo()],
            // Multi-ítem: lista de artículos a cotizar
            'items'                     => 'nullable|array',
            'items.*.descripcion'       => 'required_with:items|string|max:2000',
            'items.*.material'          => 'nullable|string|max:100',
            'items.*.cantidad'          => 'nullable|integer|min:1',
            'items.*.descripcion_extra' => 'nullable|string|max:500',
        ]);

        // Si hay ítems múltiples, la descripción principal es un resumen
        $descripcionPrincipal = $data['descripcion']
            ?? (isset($data['items'][0]) ? $data['items'][0]['descripcion'] : 'Solicitud multi-ítem');

        $solicitud = SolicitudCotizacion::create([
            'id'                 => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
            'cliente_id'         => $request->user()->id,
            'origen'             => 'cliente',
            'tipo_solicitud'     => !empty($data['items']) ? 'cotizacion' : 'cotizacion',
            'descripcion'        => $descripcionPrincipal,
            'material_preferido' => $data['material_preferido'] ?? null,
            'cantidad'           => $data['cantidad'] ?? 1,
            'uso_final'          => $data['uso_final'] ?? null,
            'estado'             => 'recibida',
            'fecha_solicitud'    => now(),
        ]);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $nombreServidor = bin2hex(random_bytes(16)) . '.' . $archivo->getClientOriginalExtension();
                $ruta = "solicitudes/{$solicitud->id}/{$nombreServidor}";
                $archivo->storeAs("solicitudes/{$solicitud->id}", $nombreServidor, 'private');

                SolicitudArchivo::create([
                    'id'              => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
                    'solicitud_id'    => $solicitud->id,
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'nombre_servidor' => $ruta,
                    'tipo_mime'       => $archivo->getMimeType(),
                    'tamano_bytes'    => $archivo->getSize(),
                ]);
            }
        }

        // Guardar ítems múltiples si vienen
        if (!empty($data['items'])) {
            foreach ($data['items'] as $itemData) {
                SolicitudItem::create([
                    'id'             => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
                    'solicitud_id'   => $solicitud->id,
                    'tipo_item'      => 'personalizado',
                    'producto_nombre'=> $itemData['descripcion'],
                    'cantidad'       => $itemData['cantidad'] ?? 1,
                    'material'       => $itemData['material'] ?? null,
                    'descripcion_extra' => $itemData['descripcion_extra'] ?? null,
                    'created_at'     => now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data'    => ['id' => $solicitud->id, 'estado' => $solicitud->estado],
            'message' => 'Solicitud enviada correctamente.',
        ], 201);
    }

    public function misSolicitudes(Request $request)
    {
        $solicitudes = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->orderByDesc('fecha_solicitud')
            ->get(['id', 'descripcion', 'estado', 'precio_final', 'fecha_respuesta', 'fecha_solicitud', 'fecha_estimada_entrega']);

        return response()->json(['success' => true, 'data' => $solicitudes]);
    }

    public function show(Request $request, string $id)
    {
        $solicitud = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->with('archivos:id,solicitud_id,nombre_original,tipo_mime')
            ->findOrFail($id);

        $archivos = $solicitud->archivos->map(fn ($a) => [
            'id'              => $a->id,
            'nombre_original' => $a->nombre_original,
            'tipo_mime'       => $a->tipo_mime,
            'url'             => "/solicitudes/{$solicitud->id}/archivos/{$a->id}",
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                     => $solicitud->id,
                'descripcion'            => $solicitud->descripcion,
                'material_preferido'     => $solicitud->material_preferido,
                'cantidad'               => $solicitud->cantidad,
                'uso_final'              => $solicitud->uso_final,
                'estado'                 => $solicitud->estado,
                'precio_final'           => $solicitud->precio_final,
                'fecha_solicitud'        => $solicitud->fecha_solicitud,
                'fecha_respuesta'        => $solicitud->fecha_respuesta,
                'fecha_estimada_entrega' => $solicitud->fecha_estimada_entrega,
                'archivos'               => $archivos,
            ],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $solicitud = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->findOrFail($id);

        if ($solicitud->estado !== 'recibida') {
            return response()->json([
                'success' => false,
                'error'   => 'La solicitud ya está siendo atendida y no se puede editar.',
            ], 403);
        }

        $data = $request->validate([
            'descripcion'        => 'required|string|max:2000',
            'material_preferido' => 'nullable|string|max:100',
            'cantidad'           => 'nullable|integer|min:1',
            'uso_final'          => 'nullable|string|max:500',
            'archivos.*'         => ['nullable', 'file', 'max:20480', $this->reglaExtensionArchivo()],
        ]);

        if ($solicitud->archivos()->count() + count($request->file('archivos', [])) > 5) {
            return response()->json([
                'success' => false,
                'error'   => 'Máximo 5 archivos por solicitud.',
            ], 422);
        }

        $solicitud->update([
            'descripcion'        => $data['descripcion'],
            'material_preferido' => $data['material_preferido'] ?? null,
            'cantidad'           => $data['cantidad'] ?? 1,
            'uso_final'          => $data['uso_final'] ?? null,
        ]);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $nombreServidor = bin2hex(random_bytes(16)) . '.' . $archivo->getClientOriginalExtension();
                $ruta = "solicitudes/{$solicitud->id}/{$nombreServidor}";
                $archivo->storeAs("solicitudes/{$solicitud->id}", $nombreServidor, 'private');

                SolicitudArchivo::create([
                    'id'              => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
                    'solicitud_id'    => $solicitud->id,
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'nombre_servidor' => $ruta,
                    'tipo_mime'       => $archivo->getMimeType(),
                    'tamano_bytes'    => $archivo->getSize(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data'    => ['id' => $solicitud->id],
            'message' => 'Solicitud actualizada correctamente.',
        ]);
    }

    public function eliminarArchivo(Request $request, string $id, string $archivoId)
    {
        $solicitud = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->findOrFail($id);

        if ($solicitud->estado !== 'recibida') {
            return response()->json([
                'success' => false,
                'error'   => 'La solicitud ya está siendo atendida y no se puede editar.',
            ], 403);
        }

        $archivo = SolicitudArchivo::where('solicitud_id', $solicitud->id)->findOrFail($archivoId);

        Storage::disk('private')->delete($archivo->nombre_servidor);
        $archivo->delete();

        return response()->json(['success' => true, 'message' => 'Archivo eliminado.']);
    }

    public function estado(Request $request, string $id)
    {
        $solicitud = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'estado'                 => $solicitud->estado,
                'precio_final'           => $solicitud->precio_final,
                'fecha_respuesta'        => $solicitud->fecha_respuesta,
                'fecha_estimada_entrega' => $solicitud->fecha_estimada_entrega,
            ],
        ]);
    }

    public function descargarArchivo(Request $request, string $solicitudId, string $archivoId)
    {
        $solicitud = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->findOrFail($solicitudId);

        $archivo = SolicitudArchivo::where('solicitud_id', $solicitud->id)
            ->findOrFail($archivoId);

        if (!Storage::disk('private')->exists($archivo->nombre_servidor)) {
            return response()->json(['success' => false, 'error' => 'Archivo no encontrado.'], 404);
        }

        return Storage::disk('private')->download($archivo->nombre_servidor, $archivo->nombre_original);
    }
}
