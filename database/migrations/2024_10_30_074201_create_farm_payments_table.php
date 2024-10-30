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
        Schema::create('farm_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms');
            $table->foreignId('payment_id')->constrained('payments');
            $table->unique(['farm_id', 'payment_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_payments');
    }
};
