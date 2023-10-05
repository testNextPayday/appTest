<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminLoanPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group Maintenance
     * @author Keania
     *
     * 
     */
    public function testAdminCanViewActiveLoans()
    {
        // fake some active loans
        $loan = factory(\App\Models\Loan::class)->create([
            'status'=>'1'
        ]);

        $response = $this->get(route('admin.loans.active'));
        $response->assertStatus(200);
        $response->assertSee(number_format($loan->amount));
    }


    /**
     * @group Maintenance
     * @author Keania
     *
     * 
     */
    public function testAdminCanViewPendingLoans()
    {
        // fake some pending loans
        $loan = factory(\App\Models\Loan::class)->create();

        $response = $this->get(route('admin.loans.pending'));
        $response->assertStatus(200);
        $response->assertSee(number_format($loan->amount));
    }

     /**
     * @group Maintenance
     * @author Keania
     *
     * 
     */
    public function testAdminCanViewFulfilledLoans()
    {
        // fake some pending loans
        $loan = factory(\App\Models\Loan::class)->create([
            'status'=>'1'
        ]);

        $employments = factory(\App\Models\Employment::class)->create([
            'user_id'=>$loan->user->id
        ]);

        // update all repayment plans
        $repaymentPlans = factory(\App\Models\RepaymentPlan::class,4)->create([
            'status'=>true,
            'loan_id'=>$loan->id
        ]);

        \Artisan::call('push:fulfill');
        
        $response = $this->get(route('admin.loans.fulfilled'));
        $response->assertStatus(200);
        $response->assertSee(number_format($loan->amount));
    }
}
