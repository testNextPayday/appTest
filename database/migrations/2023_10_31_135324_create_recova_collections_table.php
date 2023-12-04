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
        Schema::create('recova_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('loan_reference');
            $table->integer('debited_amount');
            $table->integer('recovery_fee');
            $table->integer('settlement_amount');
            $table->string('transaction_reference');
            $table->string('narration');
            $table->string('institution_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recova_collections');
    }
};
