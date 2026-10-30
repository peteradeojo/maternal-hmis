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
        Schema::table('documented_diagnoses', function (Blueprint $table) {
            $table->string('diagnoses')->nullable()->change();
            $table->foreignId('icd_id')->nullable()->constrained('icd_diagnoses', 'id');

            $table->unique(['diagnosable_type', 'diagnosable_id', 'icd_id'], 'unique_icd_to_visit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documented_diagnoses', function (Blueprint $table) {
            $table->dropUnique('unique_icd_to_visit');
            $table->dropColumn(['icd_id']);
            $table->string('diagnoses')->change();
        });
    }
};
