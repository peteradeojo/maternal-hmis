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
        Schema::table('antenatal_profiles', function (Blueprint $table) {
            $table->date('next_visit')->nullable();
            $table->json('vitals')->nullable();
            $table->boolean('awaiting_lab')->default(true);
            $table->boolean('awaiting_vitals')->default(true);
            $table->boolean('awaiting_doctor')->default(true);
            $table->dropColumn(['vdrl', 'glucose', 'pcv', 'protein', 'edema']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antenatal_profiles', function (Blueprint $table) {
            $table->dropColumn(['vitals']);
        });
    }
};
