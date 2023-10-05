<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankDetailsToStatementRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_statement_requests', function (Blueprint $table) {
            //
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_statement_requests', function (Blueprint $table) {
            //
            $table->dropColumn(['account_number', 'bank_name']);
        });
    }
}
