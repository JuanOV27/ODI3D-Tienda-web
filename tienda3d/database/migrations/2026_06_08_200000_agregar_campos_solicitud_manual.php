<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hacer cliente_id nullable: requiere soltar la FK, cambiar la columna y recrearla
        // (MySQL no permite modificar columnas que tienen FK activa directamente)
        if (!Schema::hasColumn('solicitudes_cotizacion', 'origen')) {
            // Soltar la FK existente de cliente_id
            try {
                Schema::table('solicitudes_cotizacion', function ($table) {
                    $table->dropForeign('fk_sol_cliente');
                });
            } catch (\Exception $e) {
                // Si la FK tiene otro nombre o ya no existe, continuar
            }

            // Cambiar cliente_id a nullable
            \DB::statement("ALTER TABLE solicitudes_cotizacion MODIFY cliente_id VARCHAR(255) NULL");

            // Recrear la FK como nullable (ON DELETE SET NULL)
            Schema::table('solicitudes_cotizacion', function ($table) {
                $table->foreign('cliente_id', 'fk_sol_cliente')
                      ->references('id')->on('clientes')
                      ->nullOnDelete();
            });
        }

        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {

            if (!Schema::hasColumn('solicitudes_cotizacion', 'origen')) {
                $table->enum('origen', ['cliente', 'manual'])->default('cliente')
                      ->after('cliente_id');
            }

            if (!Schema::hasColumn('solicitudes_cotizacion', 'tipo_solicitud')) {
                $table->enum('tipo_solicitud', ['cotizacion', 'compra'])->default('cotizacion')
                      ->after('origen');
            }

            if (!Schema::hasColumn('solicitudes_cotizacion', 'cliente_nombre')) {
                $table->string('cliente_nombre', 255)->nullable()->after('tipo_solicitud');
            }

            if (!Schema::hasColumn('solicitudes_cotizacion', 'cliente_telefono')) {
                $table->string('cliente_telefono', 50)->nullable()->after('cliente_nombre');
            }

            if (!Schema::hasColumn('solicitudes_cotizacion', 'cliente_whatsapp')) {
                $table->string('cliente_whatsapp', 50)->nullable()->after('cliente_telefono');
            }

            if (!Schema::hasColumn('solicitudes_cotizacion', 'cliente_email')) {
                $table->string('cliente_email', 255)->nullable()->after('cliente_whatsapp');
            }

            if (!Schema::hasColumn('solicitudes_cotizacion', 'vinculacion_pendiente')) {
                $table->boolean('vinculacion_pendiente')->default(false)->after('cliente_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->dropColumn([
                'origen', 'tipo_solicitud',
                'cliente_nombre', 'cliente_telefono', 'cliente_whatsapp', 'cliente_email',
                'vinculacion_pendiente',
            ]);
        });
    }
};
