<?php

use App\Models\StockTransaction;
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
        Schema::table('stock_count_lines', function (Blueprint $table) {
            $table->integer('counted_qty')->nullable()->change();
            $table->dropConstrainedForeignId('lot_id');
            $table->foreignId('lot_id')->nullable()->constrained('stock_lots', 'id')->change();

            $table->decimal('system_qty')->default(0);
            $table->boolean('applied')->default(false);
            $table->foreignIdFor(StockTransaction::class)->nullable()->constrained('stock_transactions', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_count_lines', function (Blueprint $table) {
            $table->integer('counted_qty')->change();
        });
    }
};
