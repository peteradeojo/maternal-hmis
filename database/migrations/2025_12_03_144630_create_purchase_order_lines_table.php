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
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('purchase_orders', 'id')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('stock_items', 'id');
            $table->decimal('qty_ordered', 14, 4);
            $table->string('unit')->nullable();
            $table->decimal('unit_cost', 14, 4)->nullable();
            $table->decimal('qty_received', 14, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
