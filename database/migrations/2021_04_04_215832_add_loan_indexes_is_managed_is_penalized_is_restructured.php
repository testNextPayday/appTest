<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoanIndexesIsManagedIsPenalizedIsRestructured extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            //
            $table->index('reference');
            $table->index('is_managed');
            $table->index('is_restructured');
            $table->index('status');
            $table->index('auto_sweeping');
            $table->index('remita_active');
            $table->index('is_penalized');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            //
            $table->dropIndex('reference');
            $table->dropIndex('is_managed');
            $table->dropIndex('is_restructured');
            $table->dropIndex('status');
            $table->dropIndex('auto_sweeping');
            $table->dropIndex('remita_active');
            $table->dropIndex('is_penalized');
        });
    }
}
