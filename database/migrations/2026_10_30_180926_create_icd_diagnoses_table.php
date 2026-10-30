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
        Schema::create('icd_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('short_description');
            $table->string('long_description');

            $table->index(['code', 'short_description']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icd_diagnoses');
    }
};
