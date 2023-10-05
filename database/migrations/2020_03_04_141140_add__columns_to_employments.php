<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToEmployments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->string("passport")->nullable()->after("work_id_card");
            $table->string("valid_Id")->nullable()->after("work_id_card");
            $table->string("bank_statement")->nullable()->after("work_id_card");
            $table->string("application_form")->nullable()->after("bank_statement");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->dropColumn(['passport','valid_id','bank_statement','application_form']);
        });
    }
}
