<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('conversations', function (Blueprint $table) {
        $table->id(); // Primary key (conversation ID)
        $table->unsignedBigInteger('sender_id');
        $table->unsignedBigInteger('receiver_id');
        $table->unsignedBigInteger('last_message_id')->nullable();
        $table->timestamps();

        // Ensure unique conversations between sender and receiver
        $table->unique(['sender_id', 'receiver_id']);

        // Add foreign keys
        $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']); // Drop the foreign key constraint
        });
        Schema::dropIfExists('conversations');
    }
};
