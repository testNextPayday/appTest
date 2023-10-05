<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test the repayment plan unconfirmation for the following
 * 1) The status of the plan changed
 * 2) If the plan was paid out the repayment status is reversed
 * 3) If the plan was paid out the investor is debited on revers
 * 4) If the loan was fulfilled it will become active again
 */

class RepaymentUnconfirmationTest extends TestCase
{
    /**
     * @group Maintenance
     * @author Keania
     * A basic unconfirmation works
     *
     * @return void
     */
    public function testBasicUnconfirmationWorks()
    {
        list($admin, $plan, $route) = $this->generateNeededVariables();

        $response = $this->actingAs($admin, 'admin')->post($route);

        $plan->refresh();

        $this->assertTrue($plan->status == false);

        $this->assertTrue($plan->date_paid == null);

        $this->assertTrue($plan->collection_mode == null);
    }


    
    /**
     * @group Maintenance
     * @author Keania
     * Test that if a loan was fulfilled it becomes active
     *
     * @return void
     */
    public function testfulfilledLoanBecomesActive()
    {
        list($admin, $plan, $route) = $this->generateNeededVariables();

        $loan = $plan->loan;

        $loan->update(['status'=>'2']);

        $response = $this->actingAs($admin, 'admin')->post($route);

        $loan->refresh();

        $this->assertTrue($loan->status == '1');

    }

    
    /**
     * @group Maintenance
     * @author Keania
     * Test investor gets debited for paid out plans
     *
     * @return void
     */
    public function testForPaidOutPlansInvestorGetsDebited()
    {
        list($admin, $plan, $route) = $this->generateNeededVariables();

        $loanRequest = $plan->loan->loanRequest;

        $loanFund = factory(\App\Models\LoanFund::class)->create(
            ['request_id'=> $loanRequest->id]
        );

        $investor = $loanFund->investor;

        $vault = $investor->vault;

        $repayment = factory(\App\Models\Repayment::class)->create(
            ['plan_id'=> $plan->id, 'fund_id'=> $loanFund->id, 'investor_id'=> $investor->id]
        );

        $response = $this->actingAs($admin, 'admin')->post($route);

        $this->assertDatabaseHas(
            'wallet_transactions', 
            [
                'owner_id'=> $investor->id,
                'owner_type'=>'App\Models\Investor',
                'direction'=> 2,
                'purse'=> 2,
                'code'=>'016'
            ]
        );

        $repayment->refresh();

        $investor->refresh();

        $this->assertTrue($repayment->reversed == 1);

        $amount  = $repayment->amount;
        $tax = $repayment->tax;
        $commission = $repayment->commission;

        $this->assertTrue($investor->vault == $vault + ($tax + $commission) - $amount);
    }

    
    /**
     * Helper classes to generate needed parameters
     *
     * @return void
     */
    protected function generateNeededVariables()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $plan = factory(\App\Models\RepaymentPlan::class)->create(
            ['paid_out'=>true]
        );

        $route = route('admin.repayment.unconfirm', ['id'=> $plan->id]);

        return [$admin, $plan, $route];
    }
}
