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
        Schema::table('stock_counts', function (Blueprint $table) {
            $table->integer('status')->default(Status::active->value);
            $table->dateTimeTz('applied_at')->nullable();
            $table->foreignIdFor(User::class, 'approved_by')->nullable()->constrained('users', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_counts', function (Blueprint $table) {
            $table->dropColumn(['status']);
            // $table->dropColumn(['status', 'applied_at', 'approved_by']);
        });
    }
};
