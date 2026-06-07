<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Cliente extends Model implements AuthenticatableContract
{
    use HasApiTokens, Authenticatable;

    protected $table = 'clientes';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;   // la tabla usa fecha_registro, no created_at/updated_at

    protected $fillable = [
        'id', 'nombre', 'email', 'telefono',
        'password_hash', 'bloqueado_hasta', 'intentos_login', 'activo',
        'acepta_terminos', 'fecha_aceptacion_terminos',
        'fecha_registro', 'ultimo_acceso',
    ];

    protected $hidden = ['password_hash', 'remember_token'];

    protected $casts = [
        'bloqueado_hasta'           => 'datetime',
        'fecha_registro'            => 'datetime',
        'ultimo_acceso'             => 'datetime',
        'fecha_aceptacion_terminos' => 'datetime',
        'activo'                    => 'boolean',
        'acepta_terminos'           => 'boolean',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}
