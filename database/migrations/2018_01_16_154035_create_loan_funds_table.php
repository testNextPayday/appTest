<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_funds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');
            $table->integer('investor_id');
            $table->integer('request_id');
            $table->integer('percentage');
            $table->double('amount');
            $table->integer('original_id')->nullable();
            $table->double('sale_amount')->nullable();
            $table->datetime('transfer_date')->nullable();
            $table->boolean('staff_fund')->default(false);
            $table->integer('staff_id')->nullable();
            $table->boolean('is_current')->default(true);
            $table->integer('status')
                ->default(1)
                ->comment('1 - Pending, 2 - Active, 3 - Cancelled, 4 - Up For Transfer, 5 - Transferred, 6 - Fulfilled');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_funds');
    }
}
