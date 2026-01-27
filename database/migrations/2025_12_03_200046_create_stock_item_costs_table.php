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
        DB::statement("CREATE TYPE item_cost_source AS ENUM('GRN', 'MANUAL', 'AUTO_ADJUST', 'TRANSFER')");
        Schema::create('stock_item_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('stock_items');
            $table->decimal('cost', 12, 4);
            $table->string('source');
            $table->foreignId('lot_id')->nullable()->constrained('stock_lots');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE stock_item_costs ALTER COLUMN source TYPE item_cost_source USING source::item_cost_source;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_item_costs');
        DB::statement("DROP TYPE item_cost_source");
    }
};
