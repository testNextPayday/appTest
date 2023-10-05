<?php

namespace Tests\Unit\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\Users\LoanFundedNotification;

class FundLoanTest extends TestCase
{
    /**
     * Test Staff Can Fund Loan Request
     *@group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffCanFund()
    {

        Notification::fake();
        
        $loanRequest = factory(\App\Models\LoanRequest::class)->create(
            ['status'=>2]
        );

        $borrower = $loanRequest->user;

        $staff = factory(\App\Models\Staff::class)->create();

        $investor = factory(\App\Models\Investor::class)->create(
            ['wallet'=> $loanRequest->amount * 2]
        );

        $wallet = $investor->wallet;

        // expected value after wallet funding
        $expectedWallet = $wallet - $loanRequest->amount;

        $staff->update(['roles'=>'manage_investors']);

        $url = route('staff.loan-requests-place-fund');

        $data = [
            'investor_id'=>$investor->id,
            'fund_percentage'=>100,
            'loan_request_id'=>$loanRequest->id,

        ];

        $response = $this->actingAs($staff, 'staff')->post($url, $data);

        $response->assertStatus(200);

        $response->assertJson(['status'=>1]);

        $investor->refresh();

        $this->assertTrue($investor->wallet == $expectedWallet);

        $loanRequest->refresh();

        // assert the requesr is now sent for setup
        $this->assertTrue($loanRequest->status == 4);

        Notification::assertSentTo($borrower, LoanFundedNotification::class);

        Notification::assertSentTo($investor, LoanFundedNotification::class);
    }


    /**
     * Fund fails with small fund
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffCannotWithLowWallet()
    {

        Notification::fake();
        
        $loanRequest = factory(\App\Models\LoanRequest::class)->create(
            ['status'=>2]
        );

        $borrower = $loanRequest->user;

        $staff = factory(\App\Models\Staff::class)->create();

        $investor = factory(\App\Models\Investor::class)->create(
            ['wallet'=> $loanRequest->amount / 2]
        );

        $wallet = $investor->wallet;

        // expected value after wallet funding
        $expectedWallet = $wallet - $loanRequest->amount;

        $staff->update(['roles'=>'manage_investors']);

        $url = route('staff.loan-requests-place-fund');

        $data = [
            'investor_id'=>$investor->id,
            'fund_percentage'=>100,
            'loan_request_id'=>$loanRequest->id,

        ];

        $response = $this->actingAs($staff, 'staff')->post($url, $data);

        $response->assertStatus(200);

        $response->assertJson(
            [
                'status'=>0, 
                'message'=>'This account does not have enough money to make this bid'
            ]
        );
    }
}
