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
        Schema::create('fin_subscr_work_prices', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->date('date_end')->nullable();
            $table->decimal('price')->nullable();
            $table->string('price_cur')->nullable();
            $table->string('status')->nullable();
            $table->text('info')->nullable();
            $table->unsignedBigInteger('subscriber_work_id');
            $table->foreign('subscriber_work_id')->references('id')->on('subscriber_works');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_subscr_work_prices');
    }
};
