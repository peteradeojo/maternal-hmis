<?php

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
        Schema::table('antenatal_profiles', function (Blueprint $table) {
            $table->dateTime('closed_on')->nullable();
            $table->foreignIdFor(User::class, 'closed_by')->nullable();
            $table->text('close_reason')->nullable();
            $table->dateTime('closed_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antenatal_profiles', function (Blueprint $table) {
            $table->dropColumn(['closed_on', 'closed_by', 'closed_date', 'close_reason']);
        });
    }
};
