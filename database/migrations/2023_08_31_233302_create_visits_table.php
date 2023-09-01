<?php

use App\Enums\Status;
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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->morphs('visit');
            $table->smallInteger('status')->default(Status::active->value);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('awaiting_vitals')->default(true);
            $table->boolean('awaiting_lab_results')->default(false);
            $table->boolean('awaiting_doctor')->default(true);
            $table->boolean('awaiting_tests')->default(false);
            $table->boolean('awaiting_pharmacy')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
