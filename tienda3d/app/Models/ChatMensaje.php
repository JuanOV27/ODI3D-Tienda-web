<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMensaje extends Model
{
    protected $table = 'chat_mensajes';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'solicitud_id', 'remitente', 'remitente_id', 'mensaje', 'leido', 'fecha',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'fecha' => 'datetime',
    ];
}
