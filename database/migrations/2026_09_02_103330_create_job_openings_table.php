<?php

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("job_openings", function (Blueprint $table) {
            $table->id();
            $table->string("slug");
            $table->string("title");
            $table->string("summary");
            $table->string("description");
            $table->json("responsibilities")->default("[]");
            $table->json("requirements")->default("[]");
            $table->boolean("is_active")->default(true);
            $table->string("footer")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("job_openings");
    }
};
