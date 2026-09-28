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
        Schema::table('vitals', function (Blueprint $table) {
            $table->unique(['recorded_date', 'recordable_type', 'recordable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('vitals', function (Blueprint $table) {
            $table->dropUnique(['recorded_date', 'recordable_type', 'recordable_id']);
        });
    }
};
