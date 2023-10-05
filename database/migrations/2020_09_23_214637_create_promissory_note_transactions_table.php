<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromissoryNoteTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promissory_note_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount');
            $table->string('code');
            $table->longtext('description');
            $table->integer('direction');
            $table->integer('promissory_note_id');
            $table->integer('investor_id');
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
        Schema::dropIfExists('promissory_note_transactions');
    }
}
