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
            //
            $table->string('complaints')->nullable();
            $table->date('return_visit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anc_visits', function (Blueprint $table) {
            //
            $table->dropColumn(['return_visit', 'complaints']);
        });
    }
};
