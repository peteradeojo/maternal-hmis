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
        Schema::create('anc_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->json('vitals')->nullable(); //->default(null);
            $table->foreignId('vitals_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->boolean('is_first_visit')->default(true);
            $table->boolean('is_first_pregnancy')->default(true);
            $table->boolean('anc_tests_done')->default(false);
            $table->boolean('anc_ultrasound_done')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anc_visits');
    }
};
