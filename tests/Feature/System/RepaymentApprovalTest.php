<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RepaymentApprovalTest extends TestCase
{
    use RefreshDatabase;

     /**
     * Checking Admin can approve uploaded repayments
     *
     * @return void
     */
    public function testAdminCannotApproveIncompleteRepayments()
    {
        list($admin, $loan, $plan, $trnx, $route) = $this->getApprovalData();
        $user = $loan->user;

        $trnxAmount = $plan->total_amount - 10;
        
        // The transaction amount is less than supposed payment
        $trnx->update(['amount'=> $trnxAmount ]);

        $data = ['repayments'=> [$trnx->id]];

        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $user->refresh();

        // Assert status of plan is now 
        $plan->refresh();

        // Assert that the money got stuck in the wallet
        $this->assertTrue($user->loan_wallet == $trnxAmount);

    }

    /**
     * Checking Admin can approve uploaded repayments
     *
     * @return void
     */
    public function testAdminCanApproveRepayments()
    {
        list($admin, $loan, $plan, $trnx, $route) = $this->getApprovalData();

        $data = ['repayments'=> [$trnx->id]];

        $response = $this->actingAs($admin, 'admin')->post($route, $data);
    
        // Assert status of plan is now 
        $plan->refresh();
       
        $this->assertTrue($plan->status == 1);

        // Assert there was an outflow to the wallet
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'loan_id'=> $loan->id,
            'plan_id'=> $plan->id,
            'amount'=> $plan->total_amount,
            'direction'=>2,
            'status'=> 2
        ]);

        $this->assertFalse($trnx->fresh()->trashed());
    
        // Assert the collection method on the transaction gets passed to the plan
        $this->assertTrue($plan->collection_mode == $trnx->collection_method);
    }

    
    /**
     * Test admin approving already paid plan deletes the transaction
     *
     * @return void
     */
    public function testAdminApproveAlreadyPaidPlanDeletesTrnx()
    {
        list($admin, $loan, $plan, $trnx, $route) = $this->getApprovalData();

        // we mark plan as paid
        $plan->update(['status'=>1]);

        $data = ['repayments'=> [$trnx->id]];

        $response = $this->actingAs($admin, 'admin')->post($route, $data);
    
        // Assert is now deleted
        $this->assertTrue($trnx->fresh()->trashed());
    
    }


    public function getApprovalData()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $loan = factory(\App\Models\Loan::class)->create(['status'=>'1']);

        $plan = factory(\App\Models\RepaymentPlan::class)->create(['status'=>0, 'loan_id'=>$loan->id]);

        // The transaction we will be approving
        $trnx = factory(\App\Models\LoanWalletTransaction::class)->create([
            'user_id'=>$loan->user->id,
            'loan_id'=> $loan->id,
            'plan_id'=>$plan->id,
            'amount'=> $plan->total_amount,
            'status'=> 1,
            'is_logged'=> true
        ]);
        
        $route = route('admin.approve.all.repayments');

        return [$admin, $loan, $plan, $trnx, $route];
    }
}
