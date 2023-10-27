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
        //
        Schema::table('antenatal_profiles', function (Blueprint $table) {
            $table->date('lmp')->nullable()->change();
            $table->date('edd')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('antenatal_profiles', function (Blueprint $table) {
            $table->date('lmp')->nullable(false)->change();
            $table->date('edd')->nullable(false)->change();
        });
    }
};
