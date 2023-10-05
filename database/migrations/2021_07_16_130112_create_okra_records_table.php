<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOkraRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('okra_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');            
            $table->string('bankId')->nullable();
            $table->string('customerId')->nullable();
            $table->string('recordId')->nullable();            
            $table->string('account_id')->nullable();
            $table->string('balance_id')->nullable();
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
        Schema::dropIfExists('okra_records');
    }
}
