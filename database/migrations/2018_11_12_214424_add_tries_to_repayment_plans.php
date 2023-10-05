<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTriesToRepaymentPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repayment_plans', function (Blueprint $table) {
            $table->integer('card_tries')->default(0);
            $table->datetime('last_try')->nullable();
            $table->boolean('last_try_status')->nullable();
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
            $table->dropColumn('card_tries');
            $table->dropColumn('last_try');
            $table->dropColumn('last_try_status');
        });
    }
}
