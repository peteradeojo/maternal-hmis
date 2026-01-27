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
        Schema::create('stock_count_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_count_id')->constrained('stock_counts', 'id')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('stock_items', 'id');
            $table->foreignId('lot_id')->constrained('stock_lots', 'id');
            $table->decimal('counted_qty', 14, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_count_lines');
    }
};
