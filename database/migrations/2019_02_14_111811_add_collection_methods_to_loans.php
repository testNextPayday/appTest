<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCollectionMethodsToLoans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            
            // The plan is to store collection methods for a loan as a JSON array
            // like so [{ code: 100, type: primary, status: 0,}]
            // Status will have options like so 0 - Not setup, 1 - Awaiting Action ( User / Admin), 2 - Ready
            // Status indicates whether a particular collection type has been processed
            // Eg for DDMs, has the customer signed and activated the mandate?
            // For IPPIS has the customer signed the authority, downloaded it and admin approved?
            // Customer requirements met?
            //
            // We also add a collection_documents method which will hold an array
            // of documents uploaded for collection like so:
            // [{ "Mandate Authority": "/public/storage/path-to-file"}]
            
            $table->string('collection_methods')->nullable();
            $table->string('collection_documents')->nullable();
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
            $table->dropColumn('collection_methods');
            $table->dropColumn('collection_documents');
        });
    }
}
