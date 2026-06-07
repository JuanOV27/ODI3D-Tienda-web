<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ModuloController extends Controller
{
    public function estado(string $nombre)
    {
        $modulo = Cache::remember("modulo_{$nombre}", 60, function () use ($nombre) {
            return DB::table('modulos')->where('nombre', $nombre)->first();
        });

        if (!$modulo) {
            return response()->json(['success' => false, 'error' => 'Módulo no encontrado.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'nombre'         => $modulo->nombre,
                'activo'         => (bool) $modulo->activo,
                'mensaje_baja'   => $modulo->mensaje_baja ?? null,
            ],
        ]);
    }
}
