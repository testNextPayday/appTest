<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlanIdStatusVerifiedIndexesToPaymentBuffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_buffers', function (Blueprint $table) {
            //
            $table->index('plan_id');
            $table->index('status');
            $table->index('verified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_buffers', function (Blueprint $table) {
            //
            $table->dropIndex('plan_id');
            $table->dropIndex('status');
            $table->dropIndex('verified');
        });
    }
}
