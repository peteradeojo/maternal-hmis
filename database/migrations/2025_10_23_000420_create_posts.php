<?php

use App\Enums\Status;
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
        Schema::connection('libsql')->create('posts', function (Blueprint $table) {
            // $table->id();
            // $table->string('title')->nullable(false);
            // $table->string('description', 64)->nullable();
            // $table->text('post');
            // $table->smallInteger('status')->default(Status::active->value);
            // $table->string('user')->nullable();
            // $table->string('image')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::connection('libsql')->dropIfExists('posts');
    }
};
