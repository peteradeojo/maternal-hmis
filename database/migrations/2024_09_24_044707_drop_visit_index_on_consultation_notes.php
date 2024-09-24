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
        Schema::table('consultation_notes', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultation_notes', function (Blueprint $table) {
            $table->foreign('visit_id')->references('id')->on('visits')->cascadeOnDelete();
        });
    }
};
