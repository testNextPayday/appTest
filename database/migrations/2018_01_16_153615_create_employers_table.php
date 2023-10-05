<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->text('state');
            $table->string('payment_date')->nullable();
            $table->integer('payment_mode')->default(1)->comment('1 - Cash, 2 - Credit/AC');
            $table->integer('percentage')->default(10);
            $table->integer('is_verified')->default(0)->comment('0 - Unverified, 1 - Under Verification, 2 - Verification Denied, 3 - Verified');
            $table->boolean('user_request')->default(false);
            $table->integer('user_id')->nullable();
            
            $table->integer('employee_count')->nullable();
            $table->string('approver_name')->nullable();
            $table->string('approver_designation')->nullable();
            $table->string('approver_phone')->nullable();
            $table->string('approver_email')->nullable();
            $table->double('rate_3')->default(10);
            $table->double('rate_6')->default(10);
            $table->double('rate_12')->default(10);
            $table->double('fees_3')->default(0);
            $table->double('fees_6')->default(0);
            $table->double('fees_12')->default(0);
            $table->integer('max_tenure')->default(12);
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
        Schema::dropIfExists('employers');
    }
}
