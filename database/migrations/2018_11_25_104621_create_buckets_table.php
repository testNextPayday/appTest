<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBucketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buckets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sweep_start_day')->nullable();
            $table->string('sweep_end_day')->nullable();
            $table->string('sweep_frequency')->nullable();
            $table->string('peak_start_day')->nullable();
            $table->string('peak_end_day')->nullable();
            $table->string('peak_frequency')->nullable();
            $table->datetime('last_sweep')->nullable();
            $table->datetime('last_peak_sweep')->nullable();
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
        Schema::dropIfExists('buckets');
    }
}
