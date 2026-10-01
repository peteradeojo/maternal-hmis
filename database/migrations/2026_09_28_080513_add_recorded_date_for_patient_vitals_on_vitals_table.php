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
        try {
            Schema::table('vitals', function (Blueprint $table) {
                $table->unique(['recorded_date', 'recordable_type', 'recordable_id']);
            });
        } catch (\Throwable $th) {
            report($th);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        try {
            Schema::table('vitals', function (Blueprint $table) {
                $table->dropUnique(['recorded_date', 'recordable_type', 'recordable_id']);
            });
        } catch (\Throwable $th) {
            report($th);
        }
    }
};
