<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToBankDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_details', function (Blueprint $table) {
            $table->string('okra_account_id')->nullable();
            $table->string('okra_balance_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_details', function (Blueprint $table) {
            $table->dropColumn(['okra_account_id']);
            $table->dropColumn(['okra_balance_id']);

        });
    }
}
