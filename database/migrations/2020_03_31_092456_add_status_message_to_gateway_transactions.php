<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusMessageToGatewayTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gateway_transactions', function (Blueprint $table) {
            //
            $table->string('status_message')->nullable();
            $table->integer('link_id')->nullable();
            $table->string('link_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gateway_transactions', function (Blueprint $table) {
            //
            $table->dropColumn(['status_message','link_id','link_type']);
            
        });
    }
}
