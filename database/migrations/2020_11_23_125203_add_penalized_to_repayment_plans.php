<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPenalizedToRepaymentPlans extends Migration
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
            $table->boolean('is_penalized')->default(false);
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
            $table->dropColumn(['is_penalized']);
        });
    }
}
