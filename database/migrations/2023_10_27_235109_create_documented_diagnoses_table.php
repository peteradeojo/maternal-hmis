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
        Schema::create('documented_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->morphs('diagnosable');
            $table->string('diagnoses');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('patients');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documented_diagnoses');
    }
};
