<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPauseSweepToLoans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            // the purpose of this column was altered
            // By default which is false means its not enabled for sweeping
            // altering it means the loan has been enabled for sweeping
            // the access name was now change in the model to enable sweep to make more logical sense
            $table->boolean('pause_sweep')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            //
            $table->dropColumns(['pause_sweep']);
        });
    }
}
