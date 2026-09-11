<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\InsuranceOrganization;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('insurance_profiles', function (Blueprint $table) {
            $table->foreignIdFor(InsuranceOrganization::class, 'orgid')->nullable()->constrained('insurance_organizations', 'id')->references('id')->on('insurance_organizations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurance_profiles', function (Blueprint $table) {
            $table->dropColumn(['orgid']);
        });
    }
};
