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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_free_trial')->default(false)->after('certificate');
            $table->timestamp('free_trial_started_at')->nullable()->after('is_free_trial');
            $table->timestamp('free_trial_ends_at')->nullable()->after('free_trial_started_at');
            $table->boolean('subscription_status')->default(false)->after('free_trial_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_free_trial');
            $table->dropColumn('free_trial_started_at');
            $table->dropColumn('free_trial_ends_at');
            $table->dropColumn('subscription_status');
        });
    }
};
