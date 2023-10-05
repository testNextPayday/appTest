<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('reference');
            $table->double('amount'); 
            $table->double('interest_percentage');
            $table->integer('duration');
            $table->string('bank_statement');
            $table->string('pay_slip')->nullable();
            $table->text('comment');
            $table->integer('employment_id')->nullable();
            
            $statusComment = '0 - Pending Employee Verification, 1 - Pending Admin Activation, ';
            $statusComment .= '2 - Active, 3 - Cancelled, 4 - Taken, ';
            $statusComment .= '5 - Verification Declined, 6 - Activation Declined';  

            //7 - Referred back to user
            
            $table->integer('status')->default(0)->comment($statusComment);
            $table->integer('percentage_left')->default(100);
            $table->date('expected_withdrawal_date');
            $table->integer('risk_rating')->nullable();
            $table->string('acceptance_code');
            $table->date('acceptance_expiry');
            $table->boolean('will_collect_incomplete')->default(false);
            
            $table->string('requestId')->nullable();
            $table->string('mandateId')->nullable();
            $table->integer('mandateStage')->default(0)->comment('0 - Not Setup, 1 - Set up but not activated, 2 - Activated');
            
            $table->double('emi')->nullable();
            
            $table->string('placer_type');
            $table->integer('placer_id')->nullable();
            
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
        Schema::dropIfExists('loan_requests');
    }
}
