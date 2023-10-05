<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenaltyEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penalty_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_id');
            $table->string('desc');
            $table->double('amount');
            $table->integer('status')->default(1); // 1 - INFLOW 2 - OUTFLOW
            $table->string('type')->nullable();
            $table->integer('plan_id')->nullable();
            $table->boolean('cancelled')->default(0);
            $table->datetime('date_paid')->nullable();
            $table->string('collection_mode')->nullable();
            $table->string('payment_proof')->nullable();
            $table->datetime('due_date');
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
        Schema::dropIfExists('penalty_entries');
    }
}
