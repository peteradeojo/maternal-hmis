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
        Schema::table('job_openings', function (Blueprint $table) {
            $table->string('description', 512)->change();
            $table->string('responsibilities', 512)->change();
            $table->string('requirements', 512)->change();
            $table->string('summary', 512)->change();
            $table->string('footer', 512)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('job_openings', function (Blueprint $table) {
            $table->string('description', 512);
            $table->json('responsibilities')->default('[]')->change();
            $table->json('requirements')->default('[]')->change();
            $table->string('summary', 512);
            $table->string('footer', 512);
        });
    }
};
