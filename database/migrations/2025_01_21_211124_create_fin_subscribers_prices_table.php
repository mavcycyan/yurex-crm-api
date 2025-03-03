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
        Schema::create('fin_subscriber_prices', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->date('date_end')->nullable();
            $table->decimal('price')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('subscriber_id');
            $table->foreign('subscriber_id')->references('id')->on('subscribers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_subscriber_prices');
    }
};
