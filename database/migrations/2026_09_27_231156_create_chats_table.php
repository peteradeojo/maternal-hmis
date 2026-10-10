<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'chat';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->text('sender');
            $table->unsignedBigInteger('senderId');
            $table->text('receiver');
            $table->text('message');
            $table->unsignedBigInteger('receiverId');

            $table->index(['senderId', 'receiverId']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('chat_messages');
    }
};
