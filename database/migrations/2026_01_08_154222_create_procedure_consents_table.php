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
        Schema::create('procedure_consents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('procedure');
            $table->foreignIdFor(Admission::class, 'admission_id')->constrained('admissions', 'id');
            $table->foreignIdFor(Patient::class, 'patient_id')->constrained('patients', 'id');
            $table->string('relationship');
            $table->string('signature_path');
            $table->json('witnesses')->default('[]');
            $table->foreignIdFor(User::class)->constrained('users', 'id');
            $table->timestamps();

            $table->unique(['admission_id', 'name', 'procedure']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedure_consents');
    }
};
