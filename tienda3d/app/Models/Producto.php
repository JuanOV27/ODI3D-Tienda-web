<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'nombre', 'descripcion', 'precio', 'categoria', 'enlace_whatsapp', 'visible', 'fecha_creacion', 'creado_por',
    ];

    protected $casts = [
        'visible'        => 'boolean',
        'precio'         => 'float',
        'fecha_creacion' => 'datetime',
    ];

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'producto_id');
    }
}
