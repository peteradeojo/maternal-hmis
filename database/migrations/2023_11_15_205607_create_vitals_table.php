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
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->morphs('recordable');
            $table->string('blood_pressure', 8)->nullable();
            $table->float('weight')->nullable();
            $table->float('temperature')->nullable();
            $table->integer('respiration')->nullable();
            $table->integer('pulse')->nullable();
            $table->foreignId('recording_user_id')->nullable()->constrained('users', 'id')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitals');
    }
};
