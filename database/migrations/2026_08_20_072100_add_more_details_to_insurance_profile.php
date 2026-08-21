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
        Schema::table('insurance_profiles', function (Blueprint $table) {
            $table->date('validity_from')->nullable();
            $table->date('validity_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurance_profiles', function (Blueprint $table) {
            $table->dropColumns(['validity_from', 'validity_to']);
        });
    }
};
