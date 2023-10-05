<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBorrowerGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrower_groups', function (Blueprint $table) {
            $table->string('leader_reference')->nullable();            
            $table->double('group_wallet')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('borrower_groups', function (Blueprint $table) {
            $table->dropColumn(['leader_reference']);           
            $table->dropColumn(['group_wallet']);
        });
    }
}
