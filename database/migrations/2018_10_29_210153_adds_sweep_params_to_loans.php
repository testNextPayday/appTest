<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddsSweepParamsToLoans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('sweep_start_day')->nullable();
            $table->string('sweep_end_day')->nullable();
            $table->string('sweep_frequency')->nullable();
            $table->string('peak_start_day')->nullable();
            $table->string('peak_end_day')->nullable();
            $table->string('peak_frequency')->nullable();
            $table->integer('duration')->nullable();
            $table->string('mandateId')->nullable();
            $table->string('requestId')->nullable();
            $table->datetime('last_sweep')->nullable();
            $table->integer('disburse_status')->default(0)->comment('0 - Unattended, 1 - Pending, 2 - Prepared, 4 - Disbursed');
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
            $table->dropColumn('sweep_start_day');
            $table->dropColumn('sweep_end_day');
            $table->dropColumn('sweep_frequency');
            $table->dropColumn('peak_start_day');
            $table->dropColumn('peak_end_day');
            $table->dropColumn('peak_frequency');
            $table->dropColumn('last_sweep');
            $table->dropColumn('duration');
            $table->dropColumn('mandateId');
            $table->dropColumn('requestId');
            $table->dropColumn('disburse_status');
        });
    }
}
