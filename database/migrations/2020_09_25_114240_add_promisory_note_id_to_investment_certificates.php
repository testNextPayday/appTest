<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromisoryNoteIdToInvestmentCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investment_certificates', function (Blueprint $table) {
            //
            $table->integer('promissory_note_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investment_certificates', function (Blueprint $table) {
            //
            $table->dropColumn(['promissory_note_id']);
        });
    }
}
