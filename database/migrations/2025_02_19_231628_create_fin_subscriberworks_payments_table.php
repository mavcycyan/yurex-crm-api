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
        Schema::create('fin_subscr_work_payments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->decimal('amount')->nullable();
            $table->string('amount_currency')->nullable();
            $table->unsignedBigInteger('fin_subscr_work_price_id');
            $table->foreign('fin_subscr_work_price_id')->references('id')->on('fin_subscr_work_prices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_subscr_work_payments');
    }
};
