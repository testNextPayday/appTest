<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankStatementRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_statement_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('request_id'); // request id returned from gateway
            $table->string('ticket_no')->nullable();
            $table->string('password')->nullable();
            $table->text('desc')->nullable();
            $table->integer('status')->comment(" 0 - Pending 1 - Ticket Sent 2 - Confirmed 3 - Statement Retrieved 4 - Faled Confirmation "); // My own defined statutes
            $table->string('status_message')->nullable();
            $table->string('request_doc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_statement_requests');
    }
}
