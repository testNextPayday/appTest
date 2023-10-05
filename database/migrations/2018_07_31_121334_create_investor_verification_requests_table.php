<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestorVerificationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investor_verification_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('investor_id');
            $table->string('reference');
            $table->string('tax_number');
            $table->string('licence');
            $table->string('registration_certificate')->nullable();
            $table->string('licence_type')->nullable()->comment('1 - CBN Licence, 2 - State Issued');
            $table->integer('status')->default(0)->comment('2 - Pending, 1 - Approved, 0 - Declined');
            $table->string('placer_type')->default('App\Models\Investor');
            $table->integer('placer_id')->nullable();
            $table->boolean('managed_account')->default(0);
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
        Schema::dropIfExists('investor_verification_requests');
    }
}
