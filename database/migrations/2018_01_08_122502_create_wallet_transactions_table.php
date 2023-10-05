<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id');
            $table->string('owner_type');
            $table->double('amount');
            $table->integer('parties')->default(2);
            $table->string('reference')->nullable();
            $table->string('code');
            $table->text('description');
            $table->tinyinteger('direction')->default(1)->comment('1 - Incoming, 2 - Outgoing');
            $table->tinyinteger('purse')->default(1)->comment('1 - Wallet, 2 - Escrow/Vault'); // When we started implementing vault repayments went to vault
            $table->integer('entity_id')->nullable();
            $table->string('entity_type')->nullable();
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
        Schema::dropIfExists('wallet_transactions');
    }
}
