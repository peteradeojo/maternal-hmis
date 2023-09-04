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
        Schema::create('documentation_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documentation_id')->constrained('documentations');
            $table->string('name');
            $table->string('dosage')->nullable();
            $table->string('duration')->nullable();
            $table->string('comment')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->smallInteger('status')->default(Status::pending->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_prescriptions');
    }
};
