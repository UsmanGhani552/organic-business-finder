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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            //farm details
            $table->foreignId('user_id')->constrained('users');
            $table->string('name');
            $table->string('location');
            $table->float('lat');
            $table->float('lng');
            $table->string('phone');
            $table->string('email');
            $table->string('website');
            $table->text('description');
            // farm timings
            $table->string('timings');
            $table->string('delivery_option');
            $table->string('image');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
