<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('employer_id');
            $table->string('position')->nullable();
            $table->date('date_employed')->nullable();
            $table->date('date_confirmed')->nullable();
            $table->string('department')->nullable();
            $table->double('monthly_salary')->nullable();
            $table->double('gross_pay')->nullable();
            $table->double('net_pay')->nullable();
            $table->string('payroll_id')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_grade')->nullable();
            $table->string('supervisor_phone')->nullable();
            $table->string('supervisor_email')->nullable();
            
            $table->string('employment_letter')->nullable();
            $table->string('confirmation_letter')->nullable();
            $table->string('work_id_card')->nullable();
            $table->boolean('is_current')->default(true);
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
        Schema::dropIfExists('employments');
    }
}
