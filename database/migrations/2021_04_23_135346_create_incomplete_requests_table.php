<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncompleteRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incomplete_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('paid_status'); // if the user has made payment
            $table->string('trnx_reference')->nullable();
            $table->text('data');
            $table->string('status_message');
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
        Schema::dropIfExists('incomplete_requests');
    }
}
