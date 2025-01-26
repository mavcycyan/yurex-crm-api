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
        Schema::create('fin_client_work_payments', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->decimal('amount')->nullable();
            $table->unsignedBigInteger('price_id');
            $table->foreign('price_id')->references('id')->on('fin_client_work_prices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_client_work_payments');
    }
};
