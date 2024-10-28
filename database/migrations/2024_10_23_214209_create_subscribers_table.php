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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('edrpou')->nullable();
            $table->date('contract_date')->nullable();
            $table->unsignedBigInteger('responsible_person_id')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('director_person_name')->nullable();
            $table->string('director_person_phone')->nullable();
            $table->string('legal_address')->nullable();
            $table->string('actual_address')->nullable();
//            $table->string('last_pay_date'); get from another table
//            $table->string('fee'); get from another table
            $table->string('info')->nullable();
            $table->foreign('responsible_person_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
