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
        Schema::create('fin_subscriber_payments', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->decimal('amount')->nullable();
            $table->unsignedBigInteger('price_id');
            $table->foreign('fin_subscriber_price_id')->references('id')->on('fin_subscriber_prices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_subscriber_payments');
    }
};
