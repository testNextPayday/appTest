<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requester_id');
            $table->string('requester_type');
            $table->string('reference');
            $table->double('amount');
            $table->enum('status', [1, 2, 3, 4])->default(1)->comment('1 - Unattended, 2 - Settled, 3 - Cancelled, 4 - Declined');
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
        Schema::dropIfExists('withdrawal_requests');
    }
}
