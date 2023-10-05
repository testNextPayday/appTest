<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArmotizedColumsToRepaymentPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repayment_plans', function (Blueprint $table) {
            //
            $table->boolean('is_new');
            $table->double('begin_balance');
            $table->double('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repayment_plans', function (Blueprint $table) {
            //
        });
    }
}
