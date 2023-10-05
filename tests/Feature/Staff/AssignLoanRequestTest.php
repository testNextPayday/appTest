<?php

namespace Tests\Feature\Staff;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignLoanRequestTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Staff with permission can assign loan request
     *Test staff can unassign 
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffWithRoleCanAssign()
    {

        Notification::fake();
        Event::fake();
        
        $staff = factory(\App\Models\Staff::class)->create();

        $loanRequest = factory(\App\Models\LoanRequest::class)->create();

        $affiliateSettings =  json_encode(
            [
            'loan_vissibility'=> 'view_all_loans', 
            'commission_method'=>'disbursement'
            ]
        );

        $affiliate = factory(\App\Models\Affiliate::class)->create(
            ['settings'=> $affiliateSettings]
        );

        $affiliate->update(['commission_rate'=>1.5]);

        $previousWallet = $affiliate->wallet;

        $staff->update(['roles'=>'manage_loan_request']);

        $data = [
            'affiliate_id'=>$affiliate->id,
            'reference'=>$loanRequest->reference
        ];

        $route = route(
            'staff.loan-requests.assign', ['reference'=>$loanRequest->reference]
        );
        
        $assignResponse = $this->actingAs($staff, 'staff')->post($route, $data);

        $assignResponse->assertStatus(302);

        $loanRequest->refresh();
        // assert the assignment works
        $this->assertTrue($loanRequest->placer_id == $affiliate->id);
        $this->assertTrue($loanRequest->placer_type == get_class($affiliate));

        $assignResponse->assertSessionHas(['success'=>'Loan Request Assigned Successfully']);

        // update the loan request to funded status

        $loanRequest->update(['percentage_left'=>0, 'status'=>4]);

        $admin = factory(\App\Models\Admin::class)->create();

        $loan = factory(\App\Models\Loan::class)->create(
            [
            'request_id'=>$loanRequest->id,
            'user_id'=>$loanRequest->user->id,
            'status'=>'0',
            'collector_type'=>$loanRequest->placer_type,
            'collector_id'=>$loanRequest->placer_id
            ]
        );

        $disburseRoute = route('admin.loans.disburse', ['loan'=>$loan->reference]);

        $disburseResponse = $this->actingAs($admin, 'admin')->get($disburseRoute);

        
        $affiliate->refresh();

        $amountAdded = ($loan->amount * ($affiliate->commission_rate/100)) + $previousWallet;


        $this->assertTrue($affiliate->wallet == $amountAdded);


    }

    
    /**
     * Test staff can unassign 
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffCanUnassign() 
    {
        Notification::fake();
        Event::fake();
        
        $staff = factory(\App\Models\Staff::class)->create();

        $affiliateSettings =  json_encode(
            [
            'loan_vissibility'=> 'view_all_loans', 
            'commission_method'=>'disbursement'
            ]
        );

        $affiliate = factory(\App\Models\Affiliate::class)->create(
            ['settings'=> $affiliateSettings]
        );

        $affiliate->update(['commission_rate'=>1.5]);

        $loanRequest = factory(\App\Models\LoanRequest::class)->create(
            [
            'placer_id'=>$affiliate->id,
            'placer_type'=>get_class($affiliate)
            ]
        );

        $staff->update(['roles'=>'manage_loan_request']);

        $data = [
            'affiliate_id'=>null,
            'reference'=>$loanRequest->reference
        ];

        $route = route(
            'staff.loan-requests.unassign', ['reference'=>$loanRequest->reference]
        );
        
        $assignResponse = $this->actingAs($staff, 'staff')->post($route, $data);

       

        $assignResponse->assertStatus(302);

        $loanRequest->refresh();

      
        // assert the assignment works
        $this->assertTrue($loanRequest->placer_id == null);
        $this->assertTrue($loanRequest->placer_type == null);

        $assignResponse->assertSessionHas(['success'=>'Loan Request Unassigned Successfully']);
    }
}
