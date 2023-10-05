<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id');
            $table->string('owner_type');
            $table->double('amount');
            $table->string('reference');
            $table->string('transaction_id');
            $table->string('description');
            $table->integer('pay_status')->default(0);
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
        Schema::dropIfExists('gateway_transactions');
    }
}
