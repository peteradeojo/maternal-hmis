<?php

use App\Enums\Status;
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
            $table->string('presentation')->nullable();
            $table->string('lie')->nullable();
            $table->string('fundal_height')->nullable();
            $table->string('fetal_heart_rate')->nullable();
            $table->string('edema')->nullable();
            $table->string('protein')->nullable();
            $table->string('glucose')->nullable();
            $table->string('vdrl')->nullable();
            $table->string('pcv')->nullable();
            $table->string('note')->nullable();
            $table->string('presentation_relationship')->nullable();
            $table->string('drugs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antenatal_profiles', function (Blueprint $table) {
            $table->dropColumn(['presentation', 'fetal_heart_rate', 'presentation_relationship', 'lie', 'fundal_height', 'edema', 'protein', 'glucose', 'vdrl', 'pcv', 'drugs', 'note']);
        });
    }
};
