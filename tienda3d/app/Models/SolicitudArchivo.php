<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudArchivo extends Model
{
    protected $table = 'solicitudes_archivos';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'solicitud_id', 'nombre_original', 'nombre_servidor', 'tipo_mime', 'tamano_bytes',
    ];
}
