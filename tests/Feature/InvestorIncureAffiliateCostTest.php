<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Settings;
use App\Models\Affiliate;
use App\Helpers\InvestorAffiliateFees;
use Illuminate\Foundation\Testing\WithFaker;
use App\Unicredit\Collection\RepaymentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvestorIncureAffiliateCostTest extends TestCase
{

    use RefreshDatabase;

    public function  setUp(): void
    {

        parent::setUp();

        Settings::create(
            ['slug'=> 'supervisor_commission_rate',
            'name'=>'Affiliate Supervisor Commission Rate',
            'value'=> 1.005
            ]
        );

        Settings::create(
            ['slug'=> 'borrower_commission_rate',
            'name'=>'Borrower Commission Rate',
            'value'=> 1.005
            ]
        );
    }
    
    /**
     * Test an investor incures the cost of loan for an affiliate
     *
     * @return void
     */
    public function testInvestorIncuresCostofLoanTrue()
    {
        $amount = 10000;

        $investor = factory(\App\Models\Investor::class)->create(
            ['wallet'=> $amount]
        );

        // some few affiliate seeds
        factory(\App\Models\Affiliate::class, 5)->create();

        $loan = factory(\App\Models\Loan::class)->create();

        $loanRequest = $loan->loanRequest;

        $loanFund  = $loanRequest->funds->first();

        $loanFund->update(['investor_id'=> $investor->id]);

        $percent = $loanFund->percentage;

        $plan = factory(\App\Models\RepaymentPlan::class)->create(
            ['loan_id'=> $loan->id, 'status'=>1]
        );

        $repaymentManager = new RepaymentManager();

        $repaymentManager->settleInvestors($loan, $plan);

        $fullRate = InvestorAffiliateFees::getFullRate($loan);

        $monthlyCharge = (($fullRate/100) * $loanFund->amount) / $loan->duration;

        $investor->refresh();

        $this->assertDatabaseHas('wallet_transactions', 
            [
                'owner_id'=> $investor->id,
                'owner_type'=> 'App\Models\Investor',
                'amount'=> $monthlyCharge,
                'code'=> config('unicredit.flow')['investor_share_affiliate_cost'],
                'direction'=> 2,
                'purse'=> 2
            ]
        );

       
    }


    
    /**
     * Test that cost of fees paid by investors all through
     * Loan duration is equal to Affiliate and Affiliate Supervisor Fees
     *
     * @return void
     */
    public function testCostOfFeesEqualsAffiliateCost()
    {
        $loanAmount = 10000;

        // some few affiliate seeds
        factory(\App\Models\Affiliate::class, 5)->create();

        $investor1 = factory(\App\Models\Investor::class)->create();

        $investor2 = factory(\App\Models\Investor::class)->create();

        $loanRequest = factory(\App\Models\LoanRequest::class)->create(
            ['amount'=> $loanAmount]
        );

        $invs1Fund = factory(\App\Models\LoanFund::class)->create(
            [
                'amount'=> $loanAmount/2, 
                'investor_id'=> $investor1->id,
                'request_id'=> $loanRequest->id,
                'percentage'=> 50,
                'status'=> 2
            ]
        );

        $invs2Fund  = factory(\App\Models\LoanFund::class)->create(
            [
                'amount'=> $loanAmount/2, 
                'investor_id'=> $investor2->id,
                'request_id'=> $loanRequest->id,
                'percentage'=> 50,
                'status'=> 2
            ]
        );

        $loan = factory(\App\Models\Loan::class)->create(
            [
                'request_id'=> $loanRequest->id,
                'amount'=> $loanAmount
            ]
        );

        // loan collector
        $fullRate = InvestorAffiliateFees::getFullRate($loan);

        $plans = factory(\App\Models\RepaymentPlan::class, $loan->duration)->create(
            ['emi'=> $loan->emi(), 'loan_id'=> $loan->id]
        );

        $code = config('unicredit.flow')['investor_share_affiliate_cost'];

        $repaymentManager = new RepaymentManager();

        foreach ($plans as $plan) {

            $plan->update(['status'=>1]);
            $repaymentManager->settleInvestors($loan, $plan);
        }

        $investor1->refresh();
        $investor2->refresh();

        $costFeesInvestor1 = $investor1->transactions->where('code', $code)->sum('amount');

        $costFeesInvestor2 = $investor2->transactions->where('code', $code)->sum('amount');

        $totalFees = $costFeesInvestor1 + $costFeesInvestor2;

        $referralFees = ($fullRate/100) * $loan->amount;


        $this->assertEquals($totalFees, $referralFees);

    }
}
