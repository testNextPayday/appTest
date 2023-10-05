<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargettables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('targettables', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->integer('targettable_id');
            $table->string('targettable_type');
            $table->integer('target_id');
            $table->boolean('met')->default(false); // if a user met the target
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
        Schema::dropIfExists('targettables');
    }
}
