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
        Schema::table('inventory_balances', function (Blueprint $table) {
            $table->dropUnique(['item_id', 'location_id', 'lot_id']);
        });

        DB::statement("CREATE UNIQUE INDEX inventory_balances_unique
ON inventory_balances (item_id, location_id, lot_id)
NULLS NOT DISTINCT;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS inventory_balances_unique;");
        Schema::table('inventory_balances', function (Blueprint $table) {
            $table->unique(['item_id', 'location_id', 'lot_id']);
        });
    }
};
