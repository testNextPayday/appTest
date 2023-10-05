<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsTopToLoanRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            //
            $table->boolean('is_top_up')->default(false);
            $table->string('loan_referenced')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            //
        });
    }
}
