<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNINToInvestorsVerificationRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investor_verification_requests', function (Blueprint $table) {
            //
            $table->string('nin_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investor_verification_requests', function (Blueprint $table) {
            //
            $table->dropColumn(['nin_number']);
        });
    }
}
