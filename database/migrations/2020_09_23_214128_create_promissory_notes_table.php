<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromissoryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promissory_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('investor_id');
            $table->string('reference');
            $table->string('interest_payment_cycle');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('maturity_date')->nullable();
            $table->integer('status')->default(1);//0- pending 1 - active, 2 - ended, 3 - rolledover, 4 - liquidated
            $table->double('maturity_value')->nullable();
            $table->double('principal');
            $table->double('current_value')->nullable();
            $table->double('payable_value')->nullable();
            $table->integer('tenure');
            $table->integer('rate');
            $table->double('interest');
            $table->integer('monthsLeft')->default(0);
            $table->integer('previous_note_id')->nullable();
            //$table->integer('investment_certificate_id');
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
        Schema::dropIfExists('promissory_notes');
    }
}
