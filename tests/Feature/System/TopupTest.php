<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use App\Events\LoanDisbursedEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopupTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @group Maintenance
     *  @author Keania
     * A basic feature test example.
     *
     * @return void
     */
    public function testLoanTopupNormalLoanWorks()
    {
        
        list($admin, $collector, $oldLoan, $paidPlans, $unpaidPlans, $newLoan, $bank) = $this->generateTestData();

        $disbursalAmount = $newLoan->disbursalAmount();

        $deficit = $newLoan->getTopupDeficit();
        // disburse backend to prevent initiating payment. Moreso the codes are reusable
        $route = route('admin.loans.disburse-backend', ['loan'=>$newLoan->reference]);
        Notification::fake();
        $response = $this->actingAs($admin, 'admin')->get($route);
       
        $newLoan->refresh();
        $oldLoan->refresh();
    
        $this->assertTrue($newLoan->disbursal_amount == $disbursalAmount);

        $this->makeAssertions($newLoan, $deficit);

        // check the old loan is now fulfilled
        $oldLoan->refresh()->update(['status'=> '2']);

        // All Old plans are now paid
        foreach($oldLoan->repaymentPlans as $plan) {
            $this->assertTrue($plan->status == 1);
        } 
        
        // Assert that the user loan wallet is zero
        $this->assertTrue($oldLoan->user->fresh()->loan_wallet == 0.0);
    }

    /**
     *  @group Maintenance
     *  @author Keania
     * A basic feature test example.
     *
     * @return void
     */
    public function testLoanTopupPenaltyLoanWorks()
    {
        
        list($admin, $collector, $oldLoan, $paidPlans, $unpaidPlans, $newLoan, $bank) = $this->generateTestData();

        // Set old loan to be on penalty
        $oldLoan->update(['is_penalized'=> true]);

        $penaltyAmount = 2000;

        // Set a negative user loan wallet
        $user = $oldLoan->user;
        $user->update(['loan_wallet'=> -$penaltyAmount]);

        $disbursalAmount = $newLoan->disbursalAmount();

        $estimatedDeficit = $this->getEstimatedPenaltyDeficit($oldLoan);

        $deficit = $newLoan->getTopupDeficit();
        // disburse backend to prevent initiating payment. Moreso the codes are reusable
        $route = route('admin.loans.disburse-backend', ['loan'=>$newLoan->reference]);
        Notification::fake();
        $response = $this->actingAs($admin, 'admin')->get($route);
       
        $newLoan->refresh();
        $oldLoan->refresh();

        // Assert estimated deficit 
        $this->assertTrue($deficit == $estimatedDeficit);
    
        $this->assertTrue($newLoan->disbursal_amount == $disbursalAmount);

        $this->makeAssertions($newLoan, $deficit);

        // check the old loan is now fulfilled
        $oldLoan->refresh()->update(['status'=> '2']);

        // All Old plans are now paid
        foreach($oldLoan->repaymentPlans as $plan) {
            $this->assertTrue($plan->status == 1);
        } 
    }
    
    /**
     * Manually calculate the deficit or set off for a loan on penalty
     *
     * @param  \App\Models\Loan $oldLoan
     * @return void
     */
    public function getEstimatedPenaltyDeficit($oldLoan)
    {
        $walletBalance = $oldLoan->repaymentPlans->where('status', 1)->last()->wallet_balance;
        // Get unpiad emis
        $plans = $oldLoan->repaymentPlans->where('status', 0);
        $emis = 0;
        foreach($plans as $plan) {
            $emis += $plan->total_amount;
        }
    
        // Loan User penalty wallet balance
        $penaltyBalance = $oldLoan->user->loan_wallet;
        
        $amount = $penaltyBalance < 0 ? abs($penaltyBalance) + $emis : $emis;

        return $walletBalance > 0 ? $amount - $walletBalance : $amount + abs($walletBalance);

    }

    
    /**
     * Make Assertions
     *
     * @param  mixed $newLoan
     * @param  mixed $deficit
     * @return void
     */
    public function makeAssertions($newLoan, $deficit)
    {
        // check there wallet transactions to show movement of funds in
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'user_id'=> $newLoan->user->id,
            'loan_id'=> $newLoan->id,
            'amount'=> $deficit,
            'direction'=> 1
        ]);
            // check there wallet transactions to show movement of funds out
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'user_id'=> $newLoan->user->id,
            'loan_id'=> $newLoan->id,
            'amount'=> $deficit,
            'direction'=> 2
        ]);
    }

    
    /**
     * Generate Test data for carryout test
     *
     * @return void
     */
    public function generateTestData()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $collector = factory(\App\Models\Affiliate::class)->create();
        // Create an active loan
        $oldLoan = factory(\App\Models\Loan::class)->create(['status'=>'1', 'collector_id'=>$collector->id, 'collector_type'=>get_class($collector)]);

        $paidPlans = factory(\App\Models\RepaymentPlan::class, 4)->create(['status'=> 1, 'loan_id'=>$oldLoan->id]);

        $unpaidPlans = factory(\App\Models\RepaymentPlan::class, 2)->create(['status'=> 0, 'loan_id'=>$oldLoan->id]);

        // Create a new loan pending
        $newLoan = factory(\App\Models\Loan::class)->create([
            'status'=>'0', 'is_top_up'=>1, 
            'top_up_loan_reference'=> $oldLoan->reference, 
            'user_id'=>$oldLoan->user->id, 'collector_id'=>$collector->id, 
            'collector_type'=>get_class($collector)
        ]);

        //loan wallet is zero
        $oldLoan->user->update(['loan_wallet'=>0]);

        // Must have a disbursement acct
        $bank = factory(\App\Models\BankDetail::class)->create(['owner_id'=> $oldLoan->user->id, 'owner_type'=> 'App\Models\User']);

        // Must have an employment used by the loanrequestupgradeservice
        $employment = factory(\App\Models\Employment::class)->create(['user_id'=> $oldLoan->user->id]);

        return [$admin, $collector, $oldLoan, $paidPlans, $unpaidPlans, $newLoan, $bank];
    }
}
