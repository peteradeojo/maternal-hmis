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
        Schema::create('admission_treatment_administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained('documentation_prescriptions', 'id');
            $table->foreignId('minister_id')->constrained('users', 'id');
            $table->foreignId('admission_id')->constrained('admissions')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_treatment_administrations');
    }
};
