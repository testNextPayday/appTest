<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resource_id')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('title');
            $table->text('description');
            $table->boolean('status')->nullable();
            $table->string('message')->nullable();
            $table->longText('data_dump')->nullable();
            $table->boolean('auto_generated')->default(true);
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
        Schema::dropIfExists('logs');
    }
}
