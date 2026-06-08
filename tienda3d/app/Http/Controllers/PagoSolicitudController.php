<?php

namespace App\Http\Controllers;

use App\Models\PagoSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para el registro de pagos de solicitudes de cotización.
 *
 * Rutas internas (sin Sanctum) — sólo accesibles desde gestion3d vía proxy.
 * Protegidas únicamente por el middleware modulo:solicitudes.
 *
 * POST /api/interno/solicitudes/{id}/pagos  → store()
 * GET  /api/interno/solicitudes/{id}/pagos  → index()
 */
class PagoSolicitudController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────
    // index — lista todos los pagos de una solicitud
    // ──────────────────────────────────────────────────────────────────────
    public function index(string $solicitudId)
    {
        $pagos = PagoSolicitud::where('solicitud_id', $solicitudId)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $pagos,
            'message' => '',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // store — registra un nuevo pago
    // ──────────────────────────────────────────────────────────────────────
    public function store(Request $request, string $solicitudId)
    {
        $validator = Validator::make($request->all(), [
            'tipo'                   => 'required|in:abono,entrega',
            'monto'                  => 'required|numeric|min:0.01',
            'fecha_pago'             => 'required|date',
            'comprobante'            => 'nullable|image|max:10240',  // max 10 MB
            'nota'                   => 'nullable|string|max:1000',
            'registrado_por'         => 'nullable|string|max:100',
            'registrado_por_nombre'  => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->errors()->first(),
            ], 422);
        }

        // Guardar comprobante si se adjuntó
        $comprobantePath = null;
        if ($request->hasFile('comprobante') && $request->file('comprobante')->isValid()) {
            $archivo = $request->file('comprobante');
            $nombreServidor = bin2hex(random_bytes(12)) . '.' . $archivo->getClientOriginalExtension();
            $archivo->storeAs("solicitudes/{$solicitudId}/pagos", $nombreServidor, 'private');
            $comprobantePath = "solicitudes/{$solicitudId}/pagos/{$nombreServidor}";
        }

        // ID con el mismo patrón del sistema (time + hash)
        $id = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9);

        $pago = PagoSolicitud::create([
            'id'                    => $id,
            'solicitud_id'          => $solicitudId,
            'tipo'                  => $request->input('tipo'),
            'monto'                 => (float) $request->input('monto'),
            'fecha_pago'            => $request->input('fecha_pago'),
            'comprobante_path'      => $comprobantePath,
            'nota'                  => $request->input('nota') ?: null,
            'registrado_por'        => $request->input('registrado_por') ?: null,
            'registrado_por_nombre' => $request->input('registrado_por_nombre') ?: null,
            'created_at'            => now(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $pago,
            'message' => 'Pago registrado correctamente',
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────
    // descargarComprobante — sirve el archivo de comprobante
    // ──────────────────────────────────────────────────────────────────────
    public function descargarComprobante(string $solicitudId, string $pagoId)
    {
        $pago = PagoSolicitud::where('solicitud_id', $solicitudId)->findOrFail($pagoId);

        if (!$pago->comprobante_path || !Storage::disk('private')->exists($pago->comprobante_path)) {
            return response()->json(['success' => false, 'error' => 'Comprobante no encontrado.'], 404);
        }

        return Storage::disk('private')->download($pago->comprobante_path);
    }
}
