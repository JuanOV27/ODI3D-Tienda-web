<?php

namespace App\Http\Controllers;

use App\Models\ChatMensaje;
use App\Models\SolicitudCotizacion;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request, string $solicitudId)
    {
        $solicitud = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->findOrFail($solicitudId);

        // Marcar como leídos los mensajes del empleado hacia el cliente
        ChatMensaje::where('solicitud_id', $solicitud->id)
            ->where('remitente', 'empleado')
            ->where('leido', false)
            ->update(['leido' => true]);

        $mensajes = ChatMensaje::where('solicitud_id', $solicitud->id)
            ->orderBy('fecha')
            ->get(['id', 'remitente', 'mensaje', 'leido', 'fecha']);

        return response()->json(['success' => true, 'data' => $mensajes]);
    }

    public function store(Request $request, string $solicitudId)
    {
        $solicitud = SolicitudCotizacion::where('cliente_id', $request->user()->id)
            ->findOrFail($solicitudId);

        $data = $request->validate([
            'mensaje' => 'required|string|max:2000',
        ]);

        $msg = ChatMensaje::create([
            'id'           => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
            'solicitud_id' => $solicitud->id,
            'remitente'    => 'cliente',
            'remitente_id' => $request->user()->id,
            'mensaje'      => $data['mensaje'],
            'leido'        => false,
            'fecha'        => now(),
        ]);

        return response()->json(['success' => true, 'data' => $msg], 201);
    }
}
