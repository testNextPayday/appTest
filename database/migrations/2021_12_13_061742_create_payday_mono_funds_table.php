<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaydayMonoFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payday_mono_funds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference')->nullable();
            $table->double('amount')->default(0.00);
            $table->string('verification_status')->nullable();
            $table->string('account_id')->nullable();
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('payday_mono_funds');
    }
}
