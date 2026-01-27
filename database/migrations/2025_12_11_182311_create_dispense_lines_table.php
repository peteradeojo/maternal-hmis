<?php

use App\Models\PrescriptionLine;
use App\Models\User;
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
        Schema::create('dispense_lines', function (Blueprint $table) {
            $table->id();
            // $table->foreignIdFor(PrescriptionLine::class, 'line_id')->constrained('prescription_lines', 'id')->cascadeOnDelete();

            $table->morphs('source');

            $table->decimal('qty_dispensed', 8, 2);
            $table->foreignIdFor(User::class, 'user_id')->constrained('users', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispense_lines');
    }
};
