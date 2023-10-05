<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {

            $table->increments('id');

            $table->string('name');

            $table->text('desc')->nullable();

            $table->double('amount');

            $table->integer('status')->default(1)->comment("1 - active, 0-inactive");

            $table->enum('occurs',["weekly","monthly","yearly"])->default("monthly");

            $table->enum("frequency",["once","always"])->default("once");
            
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
        Schema::dropIfExists('bills');
    }
}
