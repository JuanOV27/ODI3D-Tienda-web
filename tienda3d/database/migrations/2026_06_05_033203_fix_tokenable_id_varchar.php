<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Sanctum crea tokenable_id como BIGINT, pero nuestros IDs son strings.
        // Eliminamos el índice compuesto primero, luego alteramos la columna.
        DB::statement('ALTER TABLE `personal_access_tokens`
            DROP INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`');

        DB::statement('ALTER TABLE `personal_access_tokens`
            MODIFY COLUMN `tokenable_id` VARCHAR(255) NOT NULL');

        DB::statement('ALTER TABLE `personal_access_tokens`
            ADD INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`
            (`tokenable_type`, `tokenable_id`)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `personal_access_tokens`
            DROP INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`');

        DB::statement('ALTER TABLE `personal_access_tokens`
            MODIFY COLUMN `tokenable_id` BIGINT UNSIGNED NOT NULL');

        DB::statement('ALTER TABLE `personal_access_tokens`
            ADD INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`
            (`tokenable_type`, `tokenable_id`)');
    }
};
