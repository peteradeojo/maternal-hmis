<?php

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
        Schema::table('insurance_authorizations', function (Blueprint $table) {
            $table->decimal('requested_amount')->nullable();
            $table->decimal('approved_amount')->nullable();
            $table->foreignId('profile_id')->nullable()->constrained('insurance_profiles', 'id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurance_authorizations', function (Blueprint $table) {
            $table->dropColumn(['requested_amount', 'approved_amount', 'profile_id']);
            $table->dropSoftDeletes();
        });
    }
};
