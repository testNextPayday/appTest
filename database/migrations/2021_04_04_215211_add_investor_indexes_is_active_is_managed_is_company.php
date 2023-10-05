<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestorIndexesIsActiveIsManagedIsCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investors', function (Blueprint $table) {
            //
            $table->index('is_active');
            $table->index('is_managed');
            $table->index('is_company');
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investors', function (Blueprint $table) {
            //
            $table->dropIndex('is_active');
            $table->dropIndex('is_managed');
            $table->dropIndex('is_company');
            $table->dropIndex('role');
        });
    }
}
