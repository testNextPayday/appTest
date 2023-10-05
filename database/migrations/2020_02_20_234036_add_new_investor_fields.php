<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewInvestorFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investors', function (Blueprint $table) {
            //
            $table->double('vault')->default(0.0);
            $table->boolean('auto_rollover')->default(false);
            $table->boolean('auto_invest')->default(false);
            $table->string('loan_fraction')->nullable();
            $table->string('employer_loan')->nullable();
            $table->integer('loan_tenors')->nullable();
            $table->string('payback_cycle')->default('backend');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investors', function (Blueprint $table) {
            //
            $table->dropColumn(['vault','auto_invest','auto_rollover','loan_fraction','employer_loan','loan_tenors','payback_cycle']);
        });
    }
}
