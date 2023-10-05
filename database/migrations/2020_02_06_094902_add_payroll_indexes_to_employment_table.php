<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayrollIndexesToEmploymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employments', function (Blueprint $table) {
            //
            $table->index(['payroll_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employments', function (Blueprint $table) {
            //
            $table->dropIndex(['payroll_id','user_id']);
        });
    }
}
