<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestorIdReferenceIndexesToPromissoryNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promissory_notes', function (Blueprint $table) {
            //
            $table->index('investor_id');
            $table->index('reference');
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
            //
            $table->dropIndex('investor_id');
            $table->dropIndex('reference');
        });
    }
}
