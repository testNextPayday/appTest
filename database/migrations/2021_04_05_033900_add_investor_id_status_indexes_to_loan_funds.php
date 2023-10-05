<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestorIdStatusIndexesToLoanFunds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_funds', function (Blueprint $table) {
            //
            $table->index('investor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_funds', function (Blueprint $table) {
            //
            $table->dropIndex('investor_id');
            $table->dropIndex('status');
        });
    }
}
