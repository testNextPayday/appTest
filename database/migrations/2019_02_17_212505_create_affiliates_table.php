<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('reference')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('state')->nullable();
            $table->double('wallet')->default(0.0);
            $table->double('escrow')->default(0.0);
            $table->timestamp('email_verified_at')->nullable();
            $table->double('commission_rate')->nullable();
            $table->double('commission_rate_investor')->nullable();
            $table->double('min_withdrawal')->nullable();
            $table->datetime('verified_at')->nullable();
            $table->string('cv')->nullable();
            $table->boolean('status')->default(false);
            $table->integer('supervisor_id')->nullable();
            $table->string('supervisor_type')->nullable();
            $table->boolean('verification_applied')->default(false);
            $table->integer('meeting_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('affiliates');
    }
}
