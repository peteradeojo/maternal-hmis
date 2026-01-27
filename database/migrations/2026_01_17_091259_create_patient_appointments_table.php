<?php

use App\Enums\Status;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
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
        Schema::create('patient_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class)->constrained('patients', 'id');
            $table->foreignIdFor(User::class, 'booked_by')->nullable()->constrained('users', 'id');
            $table->foreignIdFor(Visit::class, 'visit_id')->nullable()->constrained('visits', 'id');
            $table->string('source')->default('manual');
            $table->smallInteger('status')->default(Status::active->value);
            $table->dateTime('appointment_date')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_appointments');
    }
};
