<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('authorization_code');
            $table->string('bin')->nullable();
            $table->string('last4');
            $table->string('exp_month');
            $table->string('exp_year');
            $table->string('channel')->nullable();
            $table->string('card_type')->nullable();
            $table->string('bank')->nullable();
            $table->string('country_code')->nullable();
            $table->string('brand')->nullable();
            $table->boolean('reusable')->default(false);
            $table->string('signature')->nullable();
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
        Schema::dropIfExists('billing_cards');
    }
}
