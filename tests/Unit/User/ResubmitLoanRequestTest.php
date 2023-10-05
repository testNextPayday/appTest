<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResubmitLoanRequestTest extends TestCase
{
    /**
     * A simple test to see if a user can successful resubmit a refferred loan
     *
     * @return void
     */
    public function testLoanResubmitWorks()
    {
        $user = factory(\App\Models\User::class)->create();

        $amount = 20000;

        $loanRequest = factory(\App\Models\LoanRequest::class)->create();

        $feePercent = $loanRequest->employment->employer->success_fee;

        $charge = fee_charge($amount, $feePercent);

        $route = route(
            'users.loan-requests.resubmit', 
            ['loanRequest'=>$loanRequest->reference]
        );

        $data = [
            'newAmount'=>$amount,
            'duration'=>5,
            'charge'=> $charge,
        ];

        $response = $this->actingAs($user)->post($route, $data);

        $loanRequest->refresh();

       

        $this->assertTrue(intval($loanRequest->amount) == $data['newAmount']);

        $this->assertTrue($loanRequest->duration == $data['duration']);

        $this->assertTrue($loanRequest->emi == $loanRequest->emi());

        $this->assertTrue($loanRequest->status == '0');
        
        $this->assertTrue(
            $loanRequest->success_fee == $charge
        );

        $response->assertStatus(302);

        $response->assertSessionHas(['success'=>'Loan Request has been queued for approval']);
    }
}
