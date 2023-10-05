<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestWithMonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invest_with_monos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference')->nullable();
            $table->double('amount')->default(0.00);
            $table->timestamp('start_date')->nullable();
            $table->integer('investor_id');
            $table->string('tenure')->nullable();
            $table->boolean('status')->default(false);
            $table->string('interest_payback_cycle')->nullable();
            $table->string('payment_type')->default('Direct Transfer To Admin');
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
        Schema::dropIfExists('invest_with_monos');
    }
}
