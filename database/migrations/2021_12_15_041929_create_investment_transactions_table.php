<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestmentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investment_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference')->nullable();
            $table->double('amount')->default(0.00);
            $table->string('verification_status')->nullable();
            $table->string('account_id')->nullable();
            $table->integer('investor_id')->nullable();
            $table->string('investor_name')->nullable();
            $table->string('investor_reference')->nullable();
            $table->string('investment_type')->nullable();
            $table->boolean('approval_status')->default(false);
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
        Schema::dropIfExists('investment_transactions');
    }
}
