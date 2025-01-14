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
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn('delivery_option');
            $table->unsignedBigInteger('delivery_option_id')->after('timings');
            $table->foreign('delivery_option_id')
                ->references('id')
                ->on('delivery_options') // Replace 'delivery_options' with the correct table name
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->addColumn('string','delivery_option');
            $table->dropForeign('delivery_option_id');
            $table->dropColumn('delivery_option_id');
            $table->string('delivery_option_id')->after('timings'); 
        });
    }
};
