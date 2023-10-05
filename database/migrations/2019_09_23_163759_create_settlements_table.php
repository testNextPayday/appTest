<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');
            $table->integer('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->double('amount');
            $table->integer('status');// 1 - paid/pending 2- confirmed  3- declined
            $table->text('status_message')->nullable();
            $table->string('collection_method');
            $table->string('payment_proof')->nullable();
            $table->string('invoice')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settlements');
    }
}
