<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillApprovalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_approval_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->default('pending');
            $table->integer('bill_id');
            $table->integer('applier_id')->nullable();
            $table->string('applier_type')->nullable();
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
        Schema::dropIfExists('bill_approval_requests');
    }
}
