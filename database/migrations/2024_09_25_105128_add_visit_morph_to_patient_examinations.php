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
        Schema::table('patient_examinations', function (Blueprint $table) {
            $table->dropColumn('documentation_id');
            // $table->dropConstrainedForeignId('document_id');
            $table->nullableMorphs('visit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_examinations', function (Blueprint $table) {
            $table->dropMorphs('visit');
            $table->bigInteger('documentation_id')->nullable();
        });
    }
};
