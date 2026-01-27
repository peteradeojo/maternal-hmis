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
        Schema::create('stock_item_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('stock_items');
            $table->string('price_type');
            $table->decimal('price', 12, 4);
            $table->string('currency')->default('NGN');
            $table->timestampTz('effective_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_item_prices');
    }
};
