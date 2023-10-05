<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use App\Services\LoanRequestUpgradeService;
use App\Services\LoanRequest\UserLoanRequestService;
use App\Services\UpfrontInterest\UpfrontInterestService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpfrontInterestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Loan Upfront Interest 
     *  @group Maintenance
     *  @author Esther
     * @return void
     */
    public function testUpfrontInterestCanBePaid()
    {
            // The loanrequest service
            $loanRequestService = new UserLoanRequestService();
            //The Upfront Interest Service
            $upfrontInterestService = new UpfrontInterestService();

             // First we need a loan Request
             $loanRequest = factory(\App\Models\LoanRequest::class)->create(
                 [
                  'duration'=> 3,
                  'upfront_interest' => 1
                ]);       

             // Get loan owner
             $user = $loanRequest->user;
     
             //create employer for the user
             $employment = factory(\App\Models\Employment::class)->create([
                 'user_id'=> $user->id
             ]);
             // enable upgrade on the employer
             $employer = $employment->employer->update(['upfront_interest' => 1]);

             $request = $this->assertTrue($employer && $loanRequest);
             if($request){
                factory(\App\Models\UpfrontInterest::class)->create([
                    'request_id'=> $loanRequest->id,
                    'user_id' => $user->id 
               ]);
             }
    }
}
