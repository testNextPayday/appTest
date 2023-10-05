<?php

namespace Tests\Unit\System;

use Carbon\Carbon;
use Tests\TestCase;
use App\Services\Penalty\PenaltyService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Services\Penalty\AccruePenaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\Users\Penalty\LoanPenalizedNotification;

class PenaltyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * We cannot penalize a loan without settings
     ** @group Maintenance
     * @author Keania
     * @return void
     */
    public function testCannotPenalizeLoanWithoutSettings()
    {
        $loan = factory(\App\Models\Loan::class)->create();

        $response = (new AccruePenaltyService)->penalizeLoan($loan);

        $this->assertFalse($response);
    }

    /**
     * Loan without repayment plans wont be penalize
     ** @group Maintenance
     * @author Keania
     * @return void
     */
    public function testCannotPenalizeLoanWithoutRepaymentPlans()
    {
        $loan = factory(\App\Models\Loan::class)->create();

        $penaltySetting = factory(\App\Models\PenaltySetting::class)->create(
            ['entity_type'=> 'App\Models\Loan', 'entity_id'=>$loan->id]
        );

        $response = (new AccruePenaltyService)->penalizeLoan($loan);

        $this->assertFalse($response);
    }

    /**
     * Loan without repayment plans wont be penalize
     ** @group Maintenance
     * @author Keania
     * @return void
     */
    public function testPenaltiesWorksByLoanSettings()
    {
        $loan = factory(\App\Models\Loan::class)->create(['status'=> '1']);

        $user = $loan->user;

        $userLoanWallet = $user->loan_wallet;

        $penaltySetting = factory(\App\Models\PenaltySetting::class)->create(
            [
                'entity_type'=> 'App\Models\Loan', 
                'entity_id'=>$loan->id, 
                'grace_period'=>5, 
                'type'=> 'P',
                'value'=> 3
            ]
        );
        
        $today = Carbon::today();

        $payday = $today->subDays(10);

        $plans = factory(\App\Models\RepaymentPlan::class, 4)->create(
            ['loan_id'=> $loan->id, 'payday'=> $payday, 'status'=> 0]
        );

        $totalAmount = 0;
        foreach($plans as $plan) {
            $totalAmount += $plan->total_amount;
        }

        $expectedPenalty = round((3/100) * $totalAmount, 2);
        $loan->fresh();
        $user->refresh();

        Notification::fake();

        $response = (new AccruePenaltyService)->penalizeLoan($loan);

        Notification::assertSentTo(
            [$loan->user], LoanPenalizedNotification::class
        );
        
        $this->assertDatabaseHas('loan_wallet_transactions', ['loan_id'=> $loan->id, 'amount'=> $expectedPenalty]);

        // Assert that loan wallet was actually debited by penalty
        $this->assertTrue(($userLoanWallet - $expectedPenalty) == $user->loan_wallet);
    }


    /**
     * Penalty settings work by employer settings
     ** @group Maintenance
     * @author Keania
     * @return void
     */
    public function testPenaltiesWorksByEmployerSettings()
    {

        $employer = factory(\App\Models\Employer::class)->create();

        $user = factory(\App\Models\User::class)->create();

        $employment = factory(\App\Models\Employment::class)->create(['employer_id'=>$employer->id, 'user_id'=>$user->id]);

        $loanRequest = factory(\App\Models\LoanRequest::class)->create(['user_id'=>$user->id, 'employment_id'=>$employment->id]);

        $loan = factory(\App\Models\Loan::class)->create(['status'=> '1', 'request_id'=>$loanRequest->id, 'user_id'=>$user->id]);

        $penaltySetting = factory(\App\Models\PenaltySetting::class)->create(
            [
                'entity_type'=> 'App\Models\Employer', 
                'entity_id'=>$employer->id, 
                'grace_period'=>5, 
                'type'=> 'P',
                'value'=> 3
            ]
        );

        $today = Carbon::today();

        $payday = $today->subDays(10);

        $plans = factory(\App\Models\RepaymentPlan::class, 4)->create(
            ['loan_id'=> $loan->id, 'payday'=> $payday, 'status'=> 0]
        );

        $totalAmount = 0;
        foreach($plans as $plan) {
            $totalAmount += $plan->total_amount;
        }

        $expectedPenalty = round((3/100) * $totalAmount, 2);
        $loan->fresh();

        Notification::fake();

        $response = (new AccruePenaltyService)->penalizeLoan($loan);

        Notification::assertSentTo(
            [$loan->user], LoanPenalizedNotification::class
        );
        
        $this->assertDatabaseHas('loan_wallet_transactions', ['loan_id'=> $loan->id, 'amount'=> $expectedPenalty]);
    }


    /**
     * Test penalty fails with less loan count
     *@group Maintenance
     * @author Keania
     * @return void
     */
    public function testPenaltyFailsWithLessEMICount()
    {
        $loan = factory(\App\Models\Loan::class)->create(['status'=> '1']);

        $penaltySetting = factory(\App\Models\PenaltySetting::class)->create(
            [
                'entity_type'=> 'App\Models\Loan', 
                'entity_id'=>$loan->id, 
                'grace_period'=>5, 
                'type'=> 'P',
                'value'=> 3,
                'unpaid_count'=> 4
            ]
        );
        
        $today = Carbon::today();

        $payday = $today->subDays(10);

        $plans = factory(\App\Models\RepaymentPlan::class, 2)->create(
            ['loan_id'=> $loan->id, 'payday'=> $payday, 'status'=> 0]
        );

        $totalAmount = 0;
        foreach($plans as $plan) {
            $totalAmount += $plan->total_amount;
        }

        $expectedPenalty = round((3/100) * $totalAmount, 2);
        $loan->fresh();

        $response = (new AccruePenaltyService)->penalizeLoan($loan);
        
        $this->assertFalse($response);
    }

    
    /**
     * A simple test that we can add credit transactions to loans on penalty
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testWeCanDissolvePenalty()
    {
        // A loan already running on penalty
        $loan = factory(\App\Models\Loan::class)->create(['status'=> '1', 'is_penalized'=>true, 'date_penalized'=>now()]);

        $user = $loan->user;

        // Set User loan wallet balance to negative
        $loanWallet = 3000;

        $penaltyService = new PenaltyService();

        $penaltyService->debitPenaltyCollection($loan, abs($loanWallet), 'Test penalty');
    
        $penaltyService->dissolvePenalty($loan);
        
        $user->refresh();

        $loan->refresh();
       
        $this->assertTrue($user->loan_wallet == 0);


    }
}
