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
        DB::statement("CREATE TYPE stock_tx_type AS ENUM ('RECEIPT', 'ISSUE', 'TRANSFER', 'ADJUSTMENT', 'RETURN', 'DISPOSAL');");

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('tx_type');

            $table->foreignId('item_id')->constrained('stock_items', 'id');
            $table->foreignId('lot_id')->nullable()->constrained('stock_lots', 'id');
            $table->decimal('quantity', 14, 4);
            $table->string('unit');
            $table->decimal('unit_cost', 12, 4);
            $table->foreignId('from_location_id')->constrained('locations', 'id');
            $table->foreignId('to_location_id')->constrained('locations', 'id');
            $table->string('related_document')->nullable();
            $table->string('reason');
            $table->foreignId('performed_by')->constrained('users', 'id');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE stock_transactions ALTER COLUMN tx_type TYPE stock_tx_type USING tx_type::stock_tx_type;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
        DB::statement('DROP TYPE stock_tx_type;');
    }
};
