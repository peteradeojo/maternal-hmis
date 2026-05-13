<?php

use App\Models\Admission;
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
        Schema::create('damas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Admission::class, 'admission_id')->constrained('admissions', 'id')->cascadeOnDelete();
            $table->foreignIdFor(Patient::class, 'patient_id')->constrained('patients', 'id');
            $table->foreignIdFor(User::class, 'user_id')->constrained('users', 'id');
            $table->string('name');
            $table->string('relationship');
            $table->string('relative_name');
            $table->string('relative_relationship');
            $table->string('nurse');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damas');
    }
};
