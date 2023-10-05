<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserRequestIndexesToBankStatementRequests extends Migration
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
            $table->index('user_id');
            $table->index('request_id');
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
            $table->dropIndex('user_id');
            $table->dropIndex('request_id');
        });
    }
}
