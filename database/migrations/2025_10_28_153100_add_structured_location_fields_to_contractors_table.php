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
        Schema::table('contractors', function (Blueprint $table) {
            // Add structured location fields
            $table->string('region')->nullable()->after('site_locations');
            $table->string('district')->nullable()->after('region');
            $table->string('ward')->nullable()->after('district');
            $table->string('street')->nullable()->after('ward');
            
            // Add indexes for quick filtering
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
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropIndex(['contractors_region_index']);
            $table->dropIndex(['contractors_district_index']);
            $table->dropIndex(['contractors_ward_index']);
            $table->dropIndex(['contractors_region_district_ward_index']);
            
            $table->dropColumn(['region', 'district', 'ward', 'street']);
        });
    }
};
