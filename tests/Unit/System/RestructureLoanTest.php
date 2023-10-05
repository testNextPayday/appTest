<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Unicredit\Armotization\LoanArmotizer;
use App\Unicredit\Armotization\RestructuringLoanArmotizer;
use UnexpectedValueException;

class RestructureLoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group Maintenance
     * @author Keania
     * 
     * @expectedException \Illuminate\Validation\ValidationException
     *  @desc This test assert that an exception is thrown when there is no duration in request
     */
    public function testRestructuringWithoutNoDurationThrowsException()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=>1
        ]);

        $admin = factory(\App\Models\Admin::class)->create();

        $loan = $plan->loan;
        
        $data = [
            'loan'=>$loan
        ];

        $response = $this->actingAs($admin,'admin')->post(route('admin.loans.restructure',['loan'=>$loan->reference]),$data);

        $response->assertStatus(422);

        
        
    }

    /**
     * @group Maintenance
     * @author Keania
     * 
     * @expectedException \Exception
     *  @desc This test is assert that an exception occurs when no loan is passed in the request
     */
    public function testRestructuringWithoutNoLoanThrowsException()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=>1
        ]);

        $loan = $plan->loan;

        $loanRepaymentCount = $loan->repaymentPlans->where('status',1)->count();
        
        $data = [
            'duration'=>3,
            'charge'=> 20000,
            'addedAmount'=>20000
        ];

        $admin = factory(\App\Models\Admin::class)->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.loans.restructure'), $data);

        $response->assertStatus(422);

        
       
    }


    /**
     * @group Maintenance
     * @author Keania
     * 
     *  @desc This test is for when a loan has  paid repayment plans .. Test verify that it works
     */
    public function testRestructureCanCreateScheduleWithPaidPlans()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=>1
        ]);

        $loan = $plan->loan;

        $loanRepaymentCount = $loan->repaymentPlans->where('status',1)->count();
        
        $data = [
            'duration'=>3,
            'charge'=> 20000,
            'addedAmount'=>20000
        ];


        $rate = $loan->interest_percentage;

        $amount = $loan->getUnpaidPrincipal() + $data['charge'] + $data['addedAmount'];

        $expectedPayments = pmt($amount, $rate, $data['duration']);

        $admin = factory(\App\Models\Admin::class)->create();

        $response = $this->actingAs($admin,'admin')->post(route('admin.loans.restructure',['loan'=>$loan->reference]),$data);

        $response->assertStatus(200);

        $response->assertJson([
            'success'=>'Loan was Successfully Rescheduled'
        ]);

        $loan->refresh();

        $this->assertTrue($expectedPayments == $loan->repaymentPlans->last()->payments);

        $expectedPlanCount = $data['duration'] + $loanRepaymentCount;

        $this->assertTrue($loan->repaymentPlans->count() == $expectedPlanCount);

        $this->assertTrue($loan->repaymentPlans->last()->balance < 1 );
    }

     /**
     * @group Maintenance
     * @author Keania
     * 
     *  @desc This test is for when a loan has no paid repayment plans .. Test verify that it works
     *         Where they exists no old plans
     */
    public function testRestructureCanCreateScheduleWithoutPaidPlans()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=> 0
        ]);
       
        $loan = $plan->loan;
        $loanRepaymentCount = $loan->repaymentPlans->where('status',1)->count();
       
        $data = [
            'duration'=>3,
            'charge'=> 20000,
            'addedAmount'=>20000
        ];


        $rate = $loan->interest_percentage;

        $amount = $loan->getUnpaidPrincipal() + $data['charge'] + $data['addedAmount'];

        $expectedPayments = pmt($amount, $rate, $data['duration']);
      

        $admin = factory(\App\Models\Admin::class)->create();

        $response = $this->actingAs($admin,'admin')->post(route('admin.loans.restructure',['loan'=>$loan->reference]),$data);

        $response->assertStatus(200);

        $response->assertJson([
            'success'=>'Loan was Successfully Rescheduled'
        ]);
       
        
        $loan->refresh();
        
        $expectedPlanCount = $data['duration'] + $loanRepaymentCount;
           
        $this->assertTrue(round($expectedPayments) == round($loan->repaymentPlans->last()->payments));

        $this->assertTrue($loan->repaymentPlans->count() == $expectedPlanCount);

        $this->assertTrue($loan->repaymentPlans->last()->balance < 1 );
    }






    
}
