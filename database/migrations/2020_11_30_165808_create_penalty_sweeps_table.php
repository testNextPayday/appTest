<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenaltySweepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penalty_sweeps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id');
            $table->integer('user_id');
            $table->double('amount');
            $table->integer('status')->default(0);
            $table->string('status_message')->nullable();
            $table->string('reference')->nullable();
            $table->text('dump')->nullable();
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
        Schema::dropIfExists('penalty_sweeps');
    }
}
