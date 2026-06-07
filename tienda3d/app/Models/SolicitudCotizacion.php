<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCotizacion extends Model
{
    protected $table = 'solicitudes_cotizacion';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    const CREATED_AT = 'fecha_solicitud';  // para orderBy convenience

    protected $fillable = [
        'id', 'cliente_id', 'descripcion', 'material_preferido', 'cantidad',
        'uso_final', 'estado', 'precio_final', 'notas_internas',
        'cotizacion_id', 'asignado_a', 'fecha_solicitud', 'fecha_respuesta',
    ];

    protected $casts = [
        'precio_final'    => 'float',
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function archivos()
    {
        return $this->hasMany(SolicitudArchivo::class, 'solicitud_id');
    }

    public function mensajes()
    {
        return $this->hasMany(ChatMensaje::class, 'solicitud_id');
    }
}
