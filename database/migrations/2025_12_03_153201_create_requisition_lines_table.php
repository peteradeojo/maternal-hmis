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
        Schema::create('requisition_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisitions', 'id')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('stock_items', 'id');
            $table->decimal('qty', 14, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_lines');
    }
};
