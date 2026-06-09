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
        'fecha_estimada_entrega', 'modalidad_pago', 'porcentaje_abono',
        // Campos para solicitudes manuales (creadas desde el panel por el empleado)
        'origen', 'tipo_solicitud',
        'cliente_nombre', 'cliente_telefono', 'cliente_whatsapp', 'cliente_email',
        'vinculacion_pendiente',
    ];

    protected $casts = [
        'precio_final'            => 'float',
        'fecha_solicitud'         => 'datetime',
        'fecha_respuesta'         => 'datetime',
        'fecha_estimada_entrega'  => 'date',
        'vinculacion_pendiente'   => 'boolean',
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

    public function items()
    {
        return $this->hasMany(SolicitudItem::class, 'solicitud_id')->orderBy('created_at');
    }

    /**
     * Nombre del cliente efectivo: si la solicitud tiene un cliente registrado
     * usa su nombre; si es manual usa el nombre capturado manualmente.
     */
    public function getClienteNombreEfectivoAttribute(): string
    {
        return $this->cliente?->nombre ?? $this->cliente_nombre ?? '(sin nombre)';
    }
}
