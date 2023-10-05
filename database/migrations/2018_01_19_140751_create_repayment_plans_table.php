<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepaymentPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repayment_plans', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('loan_id');
            $table->double('interest');
            $table->double('principal');
            $table->double('emi');
            $table->double('management_fee');
            $table->double('balance');
            $table->integer('month_no');
            $table->date('payday');
            $table->boolean('order_issued')->default(false);
            $table->boolean('status')->default(false);
            $table->string('rrr')->nullable();
            $table->string('date_paid')->nullable();
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
        Schema::dropIfExists('repayment_plans');
    }
}
