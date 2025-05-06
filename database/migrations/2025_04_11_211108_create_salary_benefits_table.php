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
        Schema::create('salary_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->constrained('client_salaries')->onDelete('cascade');
            $table->foreignId('benefit_id')->constrained('benefits')->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_benefits');
    }
};
