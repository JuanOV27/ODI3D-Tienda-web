<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitudes_cotizacion', 'modalidad_pago')) {
                $table->string('modalidad_pago', 20)->nullable()->after('precio_final');
            }
            if (!Schema::hasColumn('solicitudes_cotizacion', 'porcentaje_abono')) {
                $table->decimal('porcentaje_abono', 5, 2)->nullable()->after('modalidad_pago');
            }
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->dropColumn(['modalidad_pago', 'porcentaje_abono']);
        });
    }
};
