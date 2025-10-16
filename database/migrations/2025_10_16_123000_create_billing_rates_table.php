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
        Schema::create('billing_rates', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // residential, commercial
            $table->string('location'); // city, state, or zone
            $table->decimal('collection_fee', 10, 2); // price per collection
            $table->string('frequency')->nullable(); // daily, weekly, bi-weekly, monthly
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint to prevent duplicate category-location combinations
            $table->unique(['category', 'location', 'frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_rates');
    }
};
