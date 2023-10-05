<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestorDetailsToCerts extends Migration
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
            $table->double('tax')->default(0.0);
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('bank')->nullable();
            $table->string('address')->nullable();
            $table->string('next_kin_name')->nullable();
            $table->string('next_kin_phone')->nullable();
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
        });
    }
}
