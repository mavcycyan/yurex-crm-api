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
        Schema::create('advocate_cases', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date');
            $table->text('advocates')->nullable();
            $table->string('proc_num')->nullable();
            $table->text('plot')->nullable();
            $table->decimal('fee')->nullable();
            $table->string('result')->nullable();
            $table->text('plot_big')->nullable();
            $table->unsignedBigInteger('advocate_category_id')->nullable();
            $table->unsignedBigInteger('advocate_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('advocate_category_id')->references('id')->on('advocate_categories')->onDelete('set null');
            $table->foreign('advocate_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advocate_cases');
    }
};
