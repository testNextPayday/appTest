<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RepaymentCollectionTest extends TestCase
{

        
    /**
     * Get the prep data for the operations
     *
     * @return void
     */
    public function getPrepData()
    {
        $loan = factory(\App\Models\Loan::class)->create(['status'=> '1']);
        $plan = factory(\App\Models\RepaymentPlan::class)->create(['loan_id'=>$loan->id]);
        $data = [[
            'borrower'=> $plan->id,
            'paid_amount'=> $plan->total_amount,
            'payment_method'=> 'DDAS',
            'collection_date'=> now()->toDateString(),
            'remarks'=> 'This month upload'
        ]];

        return [$loan, $plan, $data];

    }
    
    /**
     * Carryout necessay upload Assertions
     *
     * @param  mixed $response
     * @param  mixed $loan
     * @return void
     */
    public function uploadAssertions($response, $loan, $data, $plan)
    {
        $userWallet = $loan->user->loan_wallet;

        $response->assertStatus(200);

        $loan->user->fresh();

        // Assert there is a loan wallet transaction data logged
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'amount'=> $data[0]['paid_amount'],
            'loan_id'=> $loan->id,
            'plan_id'=> $plan->id,
            'is_logged'=> true,
            'status'=> 1
        ]);

        // Assert wallet has not changed
        $this->assertTrue($userWallet == $loan->user->loan_wallet);
    }


    /**
     * Staff can upload a repayment
     *
     * @return void
     */
    public function testStaffUploadsRepayment()
    {
        $staff = factory(\App\Models\Staff::class)->create(['roles'=>'manage_repayments']);

        
        // get resuse prep data
        list($loan, $plan, $data) = $this->getPrepData();
       

        $route = route('staff.bulk-repayment');

        $response = $this->actingAs($staff, 'staff')->JSON('POST', $route, $data);

        $this->uploadAssertions($response, $loan, $data, $plan);
    }


     /**
     * Admin can upload a repayment
     *
     * @return void
     */
    public function testAdminUploadsRepayment()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        // get resuse prep data
        list($loan, $plan, $data) = $this->getPrepData();
        
        $route = route('admin.bulk-repayment');

        $response = $this->actingAs($admin, 'admin')->JSON('POST', $route, $data);

        $this->uploadAssertions($response, $loan, $data, $plan);
    }


    /**
     * Test we cannot upload for an already uploaded
     *
     * @return void
     */
    public function testAdminCannotUploadsRepaymentUploaded()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        // get resuse prep data
        list($loan, $plan, $data) = $this->getPrepData();

        // mark plan as already uploaded
        $plan->update(['uploaded'=>true]);
        
        $route = route('admin.bulk-repayment');

        $response = $this->actingAs($admin, 'admin')->JSON('POST', $route, $data);
       
        $response->assertStatus(200);

        // Assert database is empty
        $this->assertDatabaseHas('loan_wallet_transactions', []);
    }
}
