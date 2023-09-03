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
        Schema::table('anc_visits', function (Blueprint $table) {
            $table->dropColumn(['is_first_visit', 'is_first_pregnancy', 'anc_tests_done', 'anc_ultrasound_done']);
            $table->string('presentation')->nullable();
            $table->string('lie')->nullable();
            $table->string('fundal_height')->nullable();
            $table->string('fetal_heart_rate')->nullable();
            // $table->string('presentation_relationship')->nullable();
            $table->string('edema')->nullable();
            $table->string('protein')->nullable();
            $table->string('glucose')->nullable();
            $table->string('vdrl')->nullable();
            $table->string('pcv')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anc_visits', function (Blueprint $table) {
            $table->string('is_first_visit')->default(true);
            $table->string('is_first_pregnancy')->default(true);
            $table->string('anc_tests_done')->default(false);
            $table->string('anc_ultrasound_done')->default(false);

            $table->dropColumn(['maturity', 'presentation', 'lie', 'fundal_height', 'edema', 'protein', 'glucose', 'vdrl', 'pcv', 'note']);
        });
    }
};
