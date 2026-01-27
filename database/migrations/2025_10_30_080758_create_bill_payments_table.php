<?php

use App\Enums\Status;
use App\Models\Bill;
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
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bill::class, 'bill_id')->constrained()->onDelete('restrict');
            $table->foreignIdFor(User::class)->constrained()->onDelete('restrict');
            $table->dateTime('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // e.g., cash, card, insurance
            $table->string('reference')->nullable(); // e.g., transaction ID
            $table->text('notes')->nullable();
            $table->smallInteger('status')->default(Status::pending->value); // e.g., confirmed, pending
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
    }
};
