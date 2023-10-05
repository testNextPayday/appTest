<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntityAttributesToWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            //You can create withdrawals on specific items
            $table->string('entity_type')->nullable();
            $table->integer('entity_id')->nullable();
            $table->longtext('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            //
            $table->dropColumn(['entity_type', 'entity_id', 'description']);
        });
    }
}
