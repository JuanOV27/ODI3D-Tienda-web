<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudItem extends Model
{
    protected $table      = 'solicitud_items';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = false;

    protected $fillable = [
        'id', 'solicitud_id', 'tipo_item',
        'producto_id', 'producto_nombre',
        'producto_precio_unitario', 'cantidad',
        'material', 'descripcion_extra', 'datos_cotizacion',
        'created_at',
    ];

    protected $casts = [
        'producto_precio_unitario' => 'float',
        'cantidad'                 => 'integer',
        'datos_cotizacion'         => 'array',
        'created_at'               => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCotizacion::class, 'solicitud_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Subtotal calculado: precio_unitario × cantidad.
     * Devuelve null si el ítem aún no tiene precio asignado.
     */
    public function getSubtotalAttribute(): ?float
    {
        if ($this->producto_precio_unitario === null) {
            return null;
        }
        return round($this->producto_precio_unitario * $this->cantidad, 2);
    }
}
