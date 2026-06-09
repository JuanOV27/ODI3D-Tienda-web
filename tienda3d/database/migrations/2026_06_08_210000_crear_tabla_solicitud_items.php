<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('solicitud_items')) {
            Schema::create('solicitud_items', function (Blueprint $table) {
                $table->string('id')->primary();  // time()_hash

                $table->string('solicitud_id');
                $table->foreign('solicitud_id')
                      ->references('id')
                      ->on('solicitudes_cotizacion')
                      ->cascadeOnDelete();

                // ── Tipo de ítem ───────────────────────────────────────────
                // 'catalogo':    producto del catálogo tienda3d (tiene producto_id)
                // 'personalizado': modelo 3D a cotizar (sin producto_id)
                $table->enum('tipo_item', ['catalogo', 'personalizado'])->default('personalizado');

                // Solo para tipo 'catalogo'
                $table->string('producto_id')->nullable();
                $table->foreign('producto_id')
                      ->references('id')
                      ->on('productos')
                      ->nullOnDelete();

                // Snapshot del nombre (aplica a ambos tipos)
                $table->string('producto_nombre');

                // Precio unitario snapshot (catálogo) o precio cotizado (personalizado, nullable)
                $table->decimal('producto_precio_unitario', 12, 2)->nullable();

                $table->integer('cantidad')->default(1);

                // Solo para personalizado
                $table->string('material', 100)->nullable();
                $table->text('descripcion_extra')->nullable();

                // Snapshot completo del cálculo del cotizador (JSON)
                // Guardado cuando el empleado hace "Agregar a lista" desde el cotizador
                $table->json('datos_cotizacion')->nullable();

                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_items');
    }
};
