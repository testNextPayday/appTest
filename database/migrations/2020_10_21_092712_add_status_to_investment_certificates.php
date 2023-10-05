<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToInvestmentCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investment_certificates', function (Blueprint $table) {
            //
            $table->integer('status')->default(1)->comment("1 -Active 2 - Stopped");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investment_certificates', function (Blueprint $table) {
            //
            $table->dropColumn(['status']);
        });
    }
}
