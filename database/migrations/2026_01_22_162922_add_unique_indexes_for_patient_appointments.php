<?php

use App\Enums\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('patient_appointments', function (Blueprint $table) {
            DB::unprepared("CREATE UNIQUE INDEX active_appointment_constraint ON patient_appointments (patient_id, status) WHERE status = " . Status::active->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('patient_appointments', function (Blueprint $table) {
            DB::statement("DROP INDEX IF EXISTS active_appointment_constraint;");
        });
    }
};
