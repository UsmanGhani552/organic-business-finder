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
        Schema::table('farm_days', function (Blueprint $table) {
            $table->string('location')->after('timings');
            $table->string('lat')->after('location');
            $table->string('lng')->after('lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farm_days', function (Blueprint $table) {
            $table->dropColumn('location');
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
    }
};
