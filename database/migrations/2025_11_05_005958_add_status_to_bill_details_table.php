<?php

use App\Enums\Status;
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
        Schema::table('bill_details', function (Blueprint $table) {
            $table->dateTime('quoted_at')->nullable();
            $table->foreignIdFor(User::class, 'quoted_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_details', function (Blueprint $table) {
            $table->dropForeign(['quoted_by']);
            $table->dropColumn(['quoted_at', 'quoted_by']);
        });
    }
};
