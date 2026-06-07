<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ClienteAuthController extends Controller
{
    private const MAX_INTENTOS = 5;
    private const BLOQUEO_MINUTOS = 15;

    public function registro(Request $request)
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:150',
            'email'           => 'required|email|unique:clientes,email',
            'password'        => 'required|string|min:8|confirmed',
            'telefono'        => 'nullable|string|max:20',
            'acepta_terminos' => 'required|accepted',
        ], [
            'acepta_terminos.required' => 'Debes aceptar los Términos y Condiciones para registrarte.',
            'acepta_terminos.accepted' => 'Debes aceptar los Términos y Condiciones para registrarte.',
        ]);

        $cliente = Cliente::create([
            'id'                        => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 9),
            'nombre'                    => $data['nombre'],
            'email'                     => $data['email'],
            'password_hash'             => Hash::make($data['password']),
            'telefono'                  => $data['telefono'] ?? null,
            'activo'                    => 1,
            'acepta_terminos'           => 1,
            'fecha_aceptacion_terminos' => now(),
            'fecha_registro'            => now(),
        ]);

        $token = $cliente->createToken('cliente-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'cliente' => ['id' => $cliente->id, 'nombre' => $cliente->nombre, 'email' => $cliente->email],
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $cliente = Cliente::where('email', $data['email'])->first();

        if (!$cliente) {
            return response()->json(['success' => false, 'error' => 'Credenciales incorrectas.'], 401);
        }

        if ($cliente->bloqueado_hasta && Carbon::now()->lt($cliente->bloqueado_hasta)) {
            $minutos = (int) Carbon::now()->diffInMinutes($cliente->bloqueado_hasta, true);
            return response()->json([
                'success' => false,
                'error'   => "Cuenta bloqueada. Intenta en {$minutos} minutos.",
            ], 429);
        }

        if (!Hash::check($data['password'], $cliente->password_hash)) {
            $intentos = ($cliente->intentos_login ?? 0) + 1;
            $update   = ['intentos_login' => $intentos];

            if ($intentos >= self::MAX_INTENTOS) {
                $update['bloqueado_hasta'] = Carbon::now()->addMinutes(self::BLOQUEO_MINUTOS);
                $update['intentos_login']  = 0;
            }
            $cliente->update($update);

            return response()->json(['success' => false, 'error' => 'Credenciales incorrectas.'], 401);
        }

        $cliente->update(['intentos_login' => 0, 'bloqueado_hasta' => null, 'ultimo_acceso' => now()]);

        $token = $cliente->createToken('cliente-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'cliente' => ['id' => $cliente->id, 'nombre' => $cliente->nombre, 'email' => $cliente->email],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Sesión cerrada.']);
    }

    public function me(Request $request)
    {
        $c = $request->user();
        return response()->json([
            'success' => true,
            'cliente' => ['id' => $c->id, 'nombre' => $c->nombre, 'email' => $c->email, 'telefono' => $c->telefono],
        ]);
    }
}
