<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw ALTER TABLE statements to avoid requiring doctrine/dbal
        DB::statement("ALTER TABLE `clients` MODIFY `city` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `clients` MODIFY `state` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `clients` MODIFY `zip_code` VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert columns to NOT NULL. If nulls exist this will fail; run only if safe.
        DB::statement("ALTER TABLE `clients` MODIFY `city` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `clients` MODIFY `state` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `clients` MODIFY `zip_code` VARCHAR(255) NOT NULL");
    }
};
