<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRequestLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_request_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id');
            $table->integer('user_id');
            $table->integer('salary_percentage')->default(20);            
            $table->boolean('cancelled');   //denotes the decrease or increase of users salary percentage         
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
        Schema::dropIfExists('loan_request_levels');
    }
}
