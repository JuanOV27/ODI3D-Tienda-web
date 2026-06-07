<?php

namespace App\Http\Controllers;

use App\Models\SolicitudCotizacion;
use App\Models\SolicitudArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion'        => 'required|string|max:2000',
            'material_preferido' => 'nullable|string|max:100',
            'cantidad'           => 'nullable|integer|min:1',
            'uso_final'          => 'nullable|string|max:500',
            'archivos.*'         => 'nullable|file|max:20480|mimes:stl,obj,3mf,jpg,jpeg,png,pdf',
        ]);

        $solicitud = SolicitudCotizacion::create([
            'id'                 => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
            'cliente_id'         => $request->user()->id,
            'descripcion'        => $data['descripcion'],
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
            ->get(['id', 'descripcion', 'estado', 'precio_final', 'fecha_respuesta', 'fecha_solicitud']);

        return response()->json(['success' => true, 'data' => $solicitudes]);
    }

    public function estado(Request $request, string $id)
    {
        $solicitud = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'estado'          => $solicitud->estado,
                'precio_final'    => $solicitud->precio_final,
                'fecha_respuesta' => $solicitud->fecha_respuesta,
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
