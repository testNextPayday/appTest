<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentBuffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_buffers', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount');
            $table->datetime('date_paid')->nullable();
            $table->integer('status')->default(0);
            $table->integer('plan_id');
            $table->string('requestId')->nullable();
            $table->string('mandateId')->nullable();
            $table->boolean('cancelled')->default(false);
            $table->string('transaction_ref')->nullable();
            $table->string('rrr')->nullable();
            $table->string('status_message')->nullable();
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
        Schema::dropIfExists('payment_buffers');
    }
}
