<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenaltySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penalty_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->string('type')->default('P'); // P - Percentage Based F - Fixed Amount
            $table->double('value'); // value of the penalty percent or fixed amount
            $table->integer('grace_period'); // Grace period in days
            $table->integer('status')->default(1); // 1 - Active 0 - Inactive
            $table->integer('unpaid_count')->default(3);
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
        Schema::dropIfExists('penalty_settings');
    }
}
