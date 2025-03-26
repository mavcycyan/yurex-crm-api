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
        Schema::create('fin_advocate_case_prices', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->date('date_end')->nullable();
            $table->decimal('price')->nullable();
            $table->string('price_cur')->nullable();
            $table->string('name')->nullable();
            $table->longText('deadlines')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('fin_advocate_case_prices');
    }
};
