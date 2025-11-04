<?php

use App\Enums\Status;
use App\Models\Patient;
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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class, 'patient_id')->constrained()->onDelete('restrict');
            $table->string('bill_number')->unique();
            $table->dateTime('bill_date');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->smallInteger('status')->default(Status::PAID->value); // e.g., unpaid,
            $table->foreignIdFor(User::class, 'created_by')->constrained('users')->onDelete('restrict');
            $table->morphs('billable');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
