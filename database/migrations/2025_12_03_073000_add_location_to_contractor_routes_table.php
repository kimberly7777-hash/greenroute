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
        Schema::table('contractor_routes', function (Blueprint $table) {
            $table->string('region')->nullable()->after('route_name');
            $table->string('district')->nullable()->after('region');
            $table->string('ward')->nullable()->after('district');
            $table->string('street')->nullable()->after('ward');
            
            // Add indexes for performance
            $table->index('region');
            $table->index('district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractor_routes', function (Blueprint $table) {
            $table->dropIndex(['region']);
            $table->dropIndex(['district']);
            $table->dropColumn(['region', 'district', 'ward', 'street']);
        });
    }
};
