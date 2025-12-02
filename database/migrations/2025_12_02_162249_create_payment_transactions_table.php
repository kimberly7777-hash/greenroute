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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('transaction_reference')->unique(); // Internal reference
            $table->string('external_reference')->nullable(); // AzamPay Transaction ID
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('TZS');
            $table->string('provider'); // AzamPesa, Airtel, Tigo, Halotel, Bank
            $table->string('account_number')->nullable(); // Phone number or masked account
            $table->string('status')->default('pending'); // pending, success, failed, cancelled
            $table->json('payment_details')->nullable(); // Store full response for debugging
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
