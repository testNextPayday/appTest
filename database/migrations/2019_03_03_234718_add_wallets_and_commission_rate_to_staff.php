<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWalletsAndCommissionRateToStaff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->double('wallet')->default(0.0);
            $table->double('escrow')->default(0.0);
            $table->double('commission_rate')->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('wallet');
            $table->dropColumn('escrow');
            $table->dropColumn('commission_rate');
        });
    }
}
