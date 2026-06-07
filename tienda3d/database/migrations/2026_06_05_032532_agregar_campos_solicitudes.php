<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitudes_cotizacion', 'material_preferido')) {
                $table->string('material_preferido', 100)->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('solicitudes_cotizacion', 'cantidad')) {
                $table->integer('cantidad')->default(1)->after('material_preferido');
            }
            if (!Schema::hasColumn('solicitudes_cotizacion', 'uso_final')) {
                $table->string('uso_final', 500)->nullable()->after('cantidad');
            }
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->dropColumn(['material_preferido', 'cantidad', 'uso_final']);
        });
    }
};
