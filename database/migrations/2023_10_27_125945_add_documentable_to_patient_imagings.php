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
        Schema::table('patient_imagings', function (Blueprint $table) {
            $table->morphs('documentable');
            $table->dropForeign(['documentation_id']);
            $table->foreignId('documentation_id')->nullable()->change()->constrained('documentations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_imagings', function (Blueprint $table) {
            $table->dropMorphs('documentable');
            $table->dropForeign(['documentation_id']);
            $table->foreignId('documentation_id')->change()->constrained('documentations');
        });
    }
};
