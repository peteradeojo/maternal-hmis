<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prescription_lines', function (Blueprint $table) {
            $table->decimal('qty_dispensed', 8, 1)->nullable();
        });

        DB::statement('DROP INDEX IF EXISTS idx_prescription_line_items;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_lines', function (Blueprint $table) {
            $table->dropColumn(['qty_dispensed']);
        });
    }
};
