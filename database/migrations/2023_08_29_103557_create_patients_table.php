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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('card_number');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->date('dob')->nullable();
            $table->smallInteger('gender')->nullable();
            $table->smallInteger('marital_status')->nullable();
            $table->string('address')->nullable();
            $table->string('occupation')->nullable();
            $table->smallInteger('religion')->nullable();
            $table->string('email')->nullable();
            $table->string('tribe')->nullable();
            $table->string('place_of_origin')->nullable();
            $table->string('nok_name')->nullable();
            $table->string('nok_phone')->nullable();
            $table->string('nok_address')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_phone')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('spouse_educational_status')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('patient_categories');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
