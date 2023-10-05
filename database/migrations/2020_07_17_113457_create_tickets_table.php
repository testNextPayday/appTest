<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');
            $table->string('subject');
            $table->integer('owner_id');
            $table->string('owner_type');
            $table->string('type'); // an idea of the kind of ticket
            $table->string('priority'); // low medium high
            $table->integer('status')->default(1); // 1 - awaiting staf 2 - awaiting user reply 3 - closed
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
        Schema::dropIfExists('tickets');
    }
}
