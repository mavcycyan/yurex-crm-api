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
            $table->string('edrpou');
            $table->date('contract_date');
            $table->unsignedBigInteger('responsible_person_id');
            $table->string('contact_person_name');
            $table->string('contact_person_phone');
            $table->string('director_person_name');
            $table->string('director_person_phone');
            $table->string('legal_address');
            $table->string('actual_address');
//            $table->string('last_pay_date'); get from another table
//            $table->string('fee'); get from another table
            $table->string('info');
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
