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
        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('includes_organic_waste')->default(false)->after('service_type');
            $table->text('organic_waste_notes')->nullable()->after('includes_organic_waste');
            $table->string('frequency')->nullable()->after('service_type'); // daily, weekly, bi-weekly, monthly
            $table->time('scheduled_time')->nullable()->after('pickup_time');
            $table->date('scheduled_date')->nullable()->after('pickup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['includes_organic_waste', 'organic_waste_notes', 'frequency', 'scheduled_time', 'scheduled_date']);
        });
    }
};
