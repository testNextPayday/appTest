<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminLoanRequestPagesTest extends TestCase
{
     /**
     * @group Maintenance
     * @author Keania
     *
     * 
     */
    public function testAdminCanViewPendingLoanRequest()
    {
        // fake some active loans
        $loanRequest = factory(\App\Models\LoanRequest::class)->create();
        $admin = factory(\App\Models\Admin::class)->create();

        $response = $this->actingAs($admin,'admin')->get(route('admin.loan-requests.pending'));
        $response->assertStatus(200);
        $response->assertSee($loanRequest->reference);
    }


    /**
     * @group Maintenance
     * @author Keania
     *
     * 
     */
    public function testAdminCanViewPendingSetupLoanRequests()
    {
        // fake some pending loans
        $loanRequest = factory(\App\Models\LoanRequest::class)->create([
            'status'=>4
        ]);
        
        $admin = factory(\App\Models\Admin::class)->create();

        $response = $this->actingAs($admin,'admin')->get(route('admin.loan-requests.pending-setup'));
        $response->assertStatus(200);
        $response->assertSee($loanRequest->reference);
    }

    /**
     * @group Maintenance
     * @author Keania
     *
     * 
     */
    public function testAdminCanViewActiveLoanRequests()
    {
        // fake some pending loans
        $loanRequest = factory(\App\Models\LoanRequest::class)->create([
            'status'=>2
        ]);

        $admin = factory(\App\Models\Admin::class)->create();

        $response = $this->actingAs($admin,'admin')->get(route('admin.loan-requests.available'));
        $response->assertStatus(200);
        $response->assertSee($loanRequest->user->name);
    }

}
