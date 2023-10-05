<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSweepParamsToEmployers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->string('sweep_start_day')->nullable();
            $table->string('sweep_end_day')->nullable();
            $table->string('sweep_frequency')->nullable();
            $table->string('peak_start_day')->nullable();
            $table->string('peak_end_day')->nullable();
            $table->string('peak_frequency')->nullable();
            $table->datetime('last_sweep')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn('sweep_start_day');
            $table->dropColumn('sweep_end_day');
            $table->dropColumn('sweep_frequency');
            $table->dropColumn('peak_start_day');
            $table->dropColumn('peak_end_day');
            $table->dropColumn('peak_frequency');
            $table->dropColumn('last_sweep');
        });
    }
}
