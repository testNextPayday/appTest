<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('midname');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('reference')->unique();
            $table->string('address')->nullable();
            $table->integer('gender')->nullable()->comment('1 - Male, 2 - Female, 3 - Other');
            $table->string('roles')->nullable();
            $table->string('avatar');
            $table->boolean('is_active')->default(true);
            $table->integer('no_of_users')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff');
    }
}
