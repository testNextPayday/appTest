<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequiredDocsToEmployers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->string("requireDocs")->default('{"workID":true,"validID":true,"payrollID":true,"appLetter":true,"confirmLetter":true,"bankStatement":true,"passport":true}');
            $table->double("loan_limit")->after("is_primary")->default(0.00);
            $table->double("success_fee")->after("fees_12")->default(7.50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn(["requireDocs", "loan_limit", "success_fee"]);
        });
    }
}
