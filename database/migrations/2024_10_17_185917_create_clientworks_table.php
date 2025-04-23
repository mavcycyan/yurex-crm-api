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
        Schema::create('client_works', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date');
            $table->date('visit_date_end')->nullable();
            $table->string('visit_purpose');
            $table->longText('what_done')->nullable();
            $table->string('result')->nullable();
            $table->text('worker')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->integer('inactive')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_works');
    }
};
