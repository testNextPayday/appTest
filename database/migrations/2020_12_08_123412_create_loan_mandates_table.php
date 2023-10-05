<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanMandatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_mandates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('remitaTransRef')->nullable();
            $table->string('mandateId')->nullable();
            $table->integer('loan_id'); // Loan Reference
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_mandates');
    }
}
