<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('solicitudes_pagos')) {
            Schema::create('solicitudes_pagos', function (Blueprint $table) {
                $table->string('id')->primary();           // time()_hash — igual que el resto de IDs
                $table->string('solicitud_id');
                $table->foreign('solicitud_id')
                      ->references('id')
                      ->on('solicitudes_cotizacion')
                      ->cascadeOnDelete();
                $table->enum('tipo', ['abono', 'entrega']);
                $table->decimal('monto', 12, 2);
                $table->date('fecha_pago');
                $table->string('comprobante_path')->nullable();  // ruta relativa en storage/app/private
                $table->text('nota')->nullable();
                // ──────────────────────────────────────────────────────────
                // Trazabilidad: quién registró el pago (empleado / admin).
                // Se guarda snapshot del nombre para que los informes no
                // dependan de JOINs ni se rompan si el usuario se elimina.
                // ──────────────────────────────────────────────────────────
                $table->string('registrado_por')->nullable();        // id de usuarios_internos
                $table->string('registrado_por_nombre')->nullable(); // snapshot del nombre en el momento del registro
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_pagos');
    }
};
