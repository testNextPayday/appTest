<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddersToPromissoryNotes extends Migration
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
            $table->integer('adder_id')->nullable();
            $table->string('adder_type')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->double('tax')->nullable();
            $table->float('rate')->change();
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
            $table->dropColumn(['adder_id', 'adder_type', 'approved_at', 'tax']);
        });
    }
}
