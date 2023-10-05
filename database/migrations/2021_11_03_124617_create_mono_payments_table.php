<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mono_payments', function (Blueprint $table) {
            $table->increments('id');       
            $table->integer('user_id')->nullable();
            $table->integer('loan_id')->nullable();         
            $table->string('reference')->nullable();            
            $table->string('verification_status')->nullable();
            $table->string('account_id')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
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
        Schema::dropIfExists('mono_payments');
    }
}
