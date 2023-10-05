<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RepaymentDeletionTest extends TestCase
{
    /**
     * Test we can delete an uploaded repayment
     *
     * @return void
     */
    public function testWeCanDeleteUploadedRepayment()
    {
        list($admin, $loan, $plan, $trnx, $route) = $this->getApprovalData();

        $data = ['delete_repayments'=> [$trnx->id]];

        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $response->assertStatus(302);
        
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
        
        $route = route('admin.delete.all.repayments');

        return [$admin, $loan, $plan, $trnx, $route];
    }
}
