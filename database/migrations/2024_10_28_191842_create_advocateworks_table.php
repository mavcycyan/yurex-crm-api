<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up(): void
    {
        Schema::create('advocate_works', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->date('date_end')->nullable();
            $table->string('name')->nullable();
            $table->longText('what_done')->nullable();
            $table->string('result')->nullable();
            $table->text('worker')->nullable();
            $table->unsignedBigInteger('advocate_case_id');
            $table->foreign('advocate_case_id')->references('id')->on('advocate_cases');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advocate_works');
    }
};
