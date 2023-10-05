<?php

namespace Tests\Unit\Investor;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Unicredit\Collection\RepaymentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Things i will be testing here
 * 1) An investor can be setup to take management fees
 * 2) The repayment manager pays the investor with management fee 
 * 3) Test two above with 1 configured and 1 unconfigured investor
 * 4) Test two (2) above with 2 configured investors
 * 
 */
class ConfiguredInvestorTakesManagementFeeTest extends TestCase
{
    
    use RefreshDatabase;
    
    /**
     * Investor can be configured to take management fee
     *
     * @return void
     */
    public function testInvestorCanBeConfiguredToTakeupMgtFee()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $investor = factory(\App\Models\Investor::class)->create();

        $data = ['takes_mgt'=> true];

        $route = route('admin.investors.update', 
            ['investor'=> $investor->reference]
        );

        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $investor->refresh();

        $this->assertTrue($investor->takes_mgt == true);

    }

    
    /**
     * Configured investor takes up management fee
     *
     * @return void
     */
    public function testConfiguredInvestorTakesUpMgtFee()
    {
        
        $plan = factory(\App\Models\RepaymentPlan::class)->create();

        $loan = $plan->loan;

        $loanFund = $loan->loanRequest->funds()->first();

        $loanFund->update(['amount'=> $loan->amount, 'percentage'=> 100]);

        foreach ($loan->loanRequest->funds as $fund) {

            if ($fund->id != $loanFund->id) {

                $fund->delete();
            }
        }

        $investor = $loanFund->investor;

        $investor->update(['takes_mgt'=>true]);

        $repaymentManager  = new RepaymentManager();

        $repaymentManager->settleInvestors($loan, $plan);

        $totalPayment = $plan->interest + $plan->principal + $plan->management_fee;

        $amountPaid  = ($loanFund->amount/$loan->amount) * $totalPayment;

        $this->assertDatabaseHas('repayments', 
            [
                'investor_id'=> $investor->id,
                'amount'=> $amountPaid
            ]
        );
    }

    
    /**
     * Test unconfigured investor works expected
     *
     * @return void
     */
    public function testUnconfiguredWorksExpected()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create();

        $loan = $plan->loan;

        $loanFund = $loan->loanRequest->funds()->first();

        $loanFund->update(['amount'=> $loan->amount, 'percentage'=> 100]);

        foreach ($loan->loanRequest->funds as $fund) {

            if ($fund->id != $loanFund->id) {

                $fund->delete();
            }
        }

        $investor = $loanFund->investor;

        $repaymentManager  = new RepaymentManager();

        $repaymentManager->settleInvestors($loan, $plan);

        $totalPayment = $plan->interest + $plan->principal;

        $amountPaid  = ($loanFund->amount/$loan->amount) * $totalPayment;

        $this->assertDatabaseHas('repayments', 
            [
                'investor_id'=> $investor->id,
                'amount'=> $amountPaid
            ]
        );
        
    }



}
