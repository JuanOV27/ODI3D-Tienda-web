<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'producto_id', 'cliente_id', 'calificacion', 'comentario', 'aprobado', 'fecha',
    ];

    protected $casts = [
        'aprobado'    => 'boolean',
        'calificacion'=> 'integer',
        'fecha'       => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
