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
        Schema::create('inventory_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained('stock_items');
            $table->foreignId('location_id')->constrained('locations'); 
            $table->foreignId('lot_id')->nullable()->constrained('stock_lots');

            $table->decimal('qty_on_hand', 14, 4)->default(0);
            $table->timestampTz('last_updated')->default(DB::raw('now()'));
            $table->timestamps();

            $table->unique(['item_id', 'location_id', 'lot_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_balances');
    }
};
