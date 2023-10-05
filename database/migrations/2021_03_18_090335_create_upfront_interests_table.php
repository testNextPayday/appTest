<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpfrontInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upfront_interests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_id');
            $table->integer('loan_id')->nullable();
            $table->integer('user_id');            
            $table->double('interest')->nullable();
            $table->double('mgt_fee')->nullable();
            $table->double('loan_fee')->nullable();
            $table->double('total_payment')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('upfront_interests');
    }
}
