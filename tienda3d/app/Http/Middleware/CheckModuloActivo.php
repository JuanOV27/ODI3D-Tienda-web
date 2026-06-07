<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckModuloActivo
{
    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        $activo = Cache::remember("modulo_{$modulo}", 60, function () use ($modulo) {
            $row = DB::connection('mysql')->table('modulos')
                ->where('nombre', $modulo)
                ->first();
            return $row ? (bool) $row->activo : false;
        });

        if (!$activo) {
            return response()->json([
                'success' => false,
                'error'   => 'Módulo temporalmente inactivo.',
            ], 503);
        }

        return $next($request);
    }
}
