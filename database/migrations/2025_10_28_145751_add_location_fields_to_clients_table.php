<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Add location fields for site location selection
            $table->string('region')->nullable()->after('address');
            $table->string('district')->nullable()->after('region');
            $table->string('ward')->nullable()->after('district');
            $table->string('street')->nullable()->after('ward');
            
            // Add indexes for quick filtering by location
            $table->index('region');
            $table->index('district');
            $table->index('ward');
            $table->index(['region', 'district', 'ward']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['clients_region_index']);
            $table->dropIndex(['clients_district_index']);
            $table->dropIndex(['clients_ward_index']);
            $table->dropIndex(['clients_region_district_ward_index']);
            
            $table->dropColumn(['region', 'district', 'ward', 'street']);
        });
    }
};
