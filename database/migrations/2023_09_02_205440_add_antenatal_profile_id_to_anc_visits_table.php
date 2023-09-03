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
            $table->foreignId('antenatal_profile_id')->nullable()->constrained('antenatal_profiles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anc_visits', function (Blueprint $table) {
            $table->dropForeign(['antenatal_profile_id']);
            $table->dropColumn('antenatal_profile_id');
        });
    }
};
