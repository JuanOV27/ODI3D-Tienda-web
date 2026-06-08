<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitudes_cotizacion', 'fecha_estimada_entrega')) {
                $table->date('fecha_estimada_entrega')->nullable()->after('fecha_respuesta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->dropColumn('fecha_estimada_entrega');
        });
    }
};
