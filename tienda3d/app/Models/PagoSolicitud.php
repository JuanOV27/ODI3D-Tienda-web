<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoSolicitud extends Model
{
    protected $table = 'solicitudes_pagos';
    public $incrementing = false;
    protected $keyType   = 'string';
    // Solo created_at, sin updated_at
    public $timestamps   = false;

    protected $fillable = [
        'id',
        'solicitud_id',
        'tipo',
        'monto',
        'fecha_pago',
        'comprobante_path',
        'nota',
        'registrado_por',
        'registrado_por_nombre',
        'created_at',
    ];

    protected $casts = [
        'monto'      => 'decimal:2',
        'fecha_pago' => 'date:Y-m-d',
        'created_at' => 'datetime',
    ];
}
