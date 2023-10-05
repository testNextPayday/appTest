<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_wallet_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('loan_id');
            $table->integer('plan_id')->nullable(); // payments into plans are stored here
            $table->string('reference');
            $table->timestamp('collection_date')->nullable();
            $table->string('collection_method');
            $table->string('payment_proof')->nullable();
            $table->boolean('confirmed')->default(false);
            
            $table->string('code');
            $table->double('amount');
            $table->text('description');
            $table->integer('direction'); // 1 - INFLOW, 2 - OUTFLOW all in wallet direction
            $table->boolean('is_logged')->default(false);
            $table->integer('status')->default(0); // 1 -unapproved 2 - approved
            $table->boolean('is_buffered')->default(false);
            $table->integer('buffer_id')->nullable();
            $table->boolean('is_settlement')->default(false);
            $table->integer('settlement_id')->nullable();
            $table->boolean('is_topup')->default(false);
            $table->boolean('is_penalty')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_wallet_transactions');
    }
}
