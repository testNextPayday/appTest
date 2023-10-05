<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // Give the target a name
            $table->integer('days'); // duration of target
            $table->string('type'); // Loan Booking Target or Investor target
            $table->string('category'); // General or Selective
            $table->double('target'); // the target amount
            $table->date('expires_at')->nullable();
            $table->integer('status')->default(1); // 1 - active 2 -close 3 - cancelled
            $table->double('reward')->nullable(); // amount as bonus
            $table->softDeletes();
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
        Schema::dropIfExists('targets');
    }
}
