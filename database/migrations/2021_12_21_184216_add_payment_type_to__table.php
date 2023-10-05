<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentTypeToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promissory_notes', function (Blueprint $table) {            
            $table->string('payment_type')->default('Bank Transfer');
            $table->integer('invest_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promissory_notes', function (Blueprint $table) {
            $table->dropColumn(['payment_type']);
            $table->dropColumn(['invest_id']);
        });
    }
}
