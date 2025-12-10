<?php

use App\Enums\Status;
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
        Schema::create('prescription_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->nullable()->constrained('prescriptions', 'id');
            $table->foreignId('item_id')->nullable()->constrained('stock_items', 'id');
            $table->string('dosage');
            $table->string('frequency')->nullable();
            $table->smallInteger('duration')->default(1);
            $table->smallInteger('status')->default(Status::pending->value);
            $table->foreignId('dispensed_by')->nullable()->constrained('users', 'id');
            $table->foreignId('prescribed_by')->constrained('users', 'id');

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['item_id', 'dosage', 'frequency', 'duration']);
        });

        DB::statement(
            sprintf("CREATE UNIQUE INDEX idx_prescription_line_items ON prescription_lines (item_id, status) WHERE status IN (%d, %d)",
            Status::pending->value,
            Status::active->value),
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX idx_prescription_line_items;");
        Schema::dropIfExists('prescription_lines');
    }
};
