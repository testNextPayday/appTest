<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOkraSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('okra_setups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->boolean('bank_response')->default(false);
            $table->string('bankId')->nullable();
            $table->string('customerId')->nullable();
            $table->string('credit_account')->nullable();
            $table->string('debit_account')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_ref')->nullable();
            $table->double('setup_fee')->nullable();            
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
        Schema::dropIfExists('okra_setups');
    }
}
