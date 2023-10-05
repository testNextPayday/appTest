<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('investor_id');
            $table->integer('fund_id');
            $table->double('percentage')->nullable();
            $table->double('duration')->nullable();
            $table->double('amount');
            $table->boolean('staff_bid')->default(false);
            $table->integer('staff_id')->nullable();
            $table->enum('status', [1, 2, 3, 4])
                ->default(1)->comment('1 - No action, 2 - Accepted, 3 - Rejected, 4 - Cancelled');
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
        Schema::dropIfExists('bids');
    }
}
