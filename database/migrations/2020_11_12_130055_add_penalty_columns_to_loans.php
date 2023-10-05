<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPenaltyColumnsToLoans extends Migration
{

  

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('loans', function (Blueprint $table) {
            //
            $table->double('penalty_balance')->nullable();
            $table->integer('is_penalized')->default(0);
            $table->datetime('date_penalized')->nullable();
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
            $table->dropColumn(['penalty_balance', 'is_penalized', 'date_penalized']);
        });
    }
}
