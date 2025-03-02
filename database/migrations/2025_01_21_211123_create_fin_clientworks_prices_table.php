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
        Schema::create('fin_client_work_prices', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->date('date_end')->nullable();
            $table->decimal('price')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('client_work_id');
            $table->foreign('client_work_id')->references('id')->on('client_works');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fin_client_work_prices');
    }
};
