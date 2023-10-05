<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('password')->nullable();
            $table->date('dob')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('gender')->nullable()->comment('1 - Male, 2 - Female, 3 - Other');
            $table->string('bvn')->nullable();
            $table->boolean('is_company')->default(false);
        
            //location information
            $table->string('occupation')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('lga')->nullable();
            $table->string('state')->nullable();
            
            //unicredit info
            $table->double('wallet')->default(0.00);
            $table->double('escrow')->default(0.00);
        
            //docs
            $table->string('passport')->nullable();
            $table->string('govt_id_card')->nullable();
            
            //family information
            $table->integer('marital_status')->nullable()->comment('1 - Single, 2 - Married, 3 - Divorced, 4 - Widowed');
            $table->integer('no_of_children')->default(0);
            $table->string('next_of_kin')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('next_of_kin_address')->nullable();
            $table->string('relationship_with_next_of_kin')->nullable();
            
            
            $table->string('phone_verification_code')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->string('remita_auth_code')->nullable();
            $table->string('remita_id')->nullable();
            
            //status flags
            $table->boolean('phone_verified')->default(false);
            $table->boolean('email_verified')->default(false);
            $table->boolean('social_verified')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_managed')->default(false);
        
            //social login
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
           
            // $table->integer('adder')->default(1)->comment('1 - User, 2 - Admin, 3 - Staff');
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
        Schema::dropIfExists('users');
    }
}
