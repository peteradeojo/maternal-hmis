
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
        Schema::create('operation_notes', function (Blueprint $table) {
            $table->id();
            $table->string('unit')->nullable();
            $table->string('consultant');
            $table->date('operation_date');
            $table->string('surgeons');
            $table->string('assistants');
            $table->string('scrub_nurse');
            $table->string('circulating_nurse')->nullable();
            $table->string('anaesthesists')->nullable();
            $table->string('anaesthesia_type')->nullable();
            $table->text('indication');
            $table->text('incision')->nullable();
            $table->text('findings');
            $table->text('procedure');
            $table->foreignIdFor(Patient::class, 'patient_id')->constrained('patients', 'id');
            $table->foreignIdFor(User::class, 'user_id')->constrained('users', 'id');
            $table->foreignIdFor(Admission::class, 'admission_id')->constrained('admissions', 'id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_notes');
    }
};
