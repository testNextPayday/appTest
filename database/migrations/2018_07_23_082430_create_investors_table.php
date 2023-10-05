<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference')->unique();
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->text('bio')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('lga')->nullable();
            $table->string('state')->nullable();
            
            //unicredit info
            $table->double('wallet')->default(0.00);
            $table->double('escrow')->default(0.00);
            $table->double('commission_rate')->default(30.0);
            
            $table->string('email_verification_code')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_managed')->default(false);
            $table->boolean('is_company')->default(false);
            
            //investor verification
            $table->string('tax_number')->nullable();
            $table->string('licence')->nullable();
            $table->string('licence_type')->nullable()->comment('1 - CBN Licence, 2 - State Issued');
            $table->string('reg_cert')->nullable();
            
            $table->string('adder_type')->default('App\Models\Investor');
            $table->integer('adder_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('investors');
    }
}
