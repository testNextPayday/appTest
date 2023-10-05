<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestmentCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investment_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');
            $table->string('name');
            $table->date('start_date');
            $table->date('maturity_date');
            $table->double('amount');
            $table->double('rate');
            $table->string('interest_payment_cycle');
            $table->string('certificate');
            $table->string('email')->nullable();
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
        Schema::dropIfExists('investment_certificates');
    }
}
