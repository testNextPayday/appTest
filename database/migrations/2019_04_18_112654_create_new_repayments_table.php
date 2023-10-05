<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewRepaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_repayments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('loan_id');
            $table->string('amount');
            $table->string('payment_method');
            $table->date('collection_date');
            $table->string('collector');
            $table->string('description');
            $table->boolean('is_verified')->default(false);
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
        Schema::dropIfExists('new_repayments');
    }
}
