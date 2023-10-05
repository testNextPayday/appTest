<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\WithdrawalRequest;
use App\Models\User;

class WithdrawalRequestTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test **/
    public function a_withdrawal_request_has_a_requester()
    {
        $investorRequest = create(WithdrawalRequest::class);
        
        $this->assertInstanceOf('App\Models\Investor', $investorRequest->requester);
        
        $borrower = create(User::class);
        
        $borrowerRequest = create(WithdrawalRequest::class, [
            'requester_id' => $borrower->id,
            'requester_type' => User::class
        ]);
        
        $this->assertInstanceOf('App\Models\User', $borrowerRequest->requester);
    }
}
