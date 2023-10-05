<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Unicredit\Managers\PayInvestorManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayInvestorManagerTest extends TestCase
{
    use RefreshDatabase;


    public function setUp(): void
    {
        parent::setUp();

        $this->investorPayer = new PayInvestorManager();
    }

    /**
     * @group Maintenance
     *  @author Keania
     * 
     * @return void
     */
    public function testInvestorGetRepaymentCollectionPlan()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=>true
        ]);
        // set loan as active
        $plan->loan->update(['status'=>'1']);
        $investor = $plan->loan->loanRequest->investors()->first();
       
        $assertPlan = $investor->repaymentPlanCollection()->first();
        
        $this->assertTrue($plan->id === $assertPlan->id);
    }

     /**
     * @group Maintenance
     *  @author Current
     *  * 
     * @return void
     */
    public function testRepaymentPlanStatusChangesToPaidOut()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=>true
        ]);

         // set loan as active
        $plan->loan->update(['status'=>'1']);
        $investor = $plan->loan->loanRequest->investors()->first();
        $loanRequest = $plan->loan->loanRequest;

      
        $vault = $investor->vault;
            
        $vaultIncrement  = refresh_vault($investor,$plan);

        $this->investorPayer->issueRepaymentProcess();
       
        $this->assertTrue($plan->fresh()->paid_out == true);
        $this->assertTrue(round($investor->fresh()->vault) == round($vault + $vaultIncrement));
    }


     /**
     * @group Maintenance
     *  @author Keania
     *  * 
     * @return void
     */
    public function testQuarterlyInvestorsGetPaid()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=>true
        ]);
         // set loan as active
         $plan->loan->update(['status'=>'1']);
        $investor = $plan->loan->loanRequest->investors()->first();
        $investor->update(['payback_cycle'=>'quarterly']);
        $vault = $investor->vault;
       
        $vaultIncrement  = refresh_vault($investor,$plan);

        $this->investorPayer->issueRepaymentProcess('quarterly');
       
        $this->assertTrue($plan->fresh()->paid_out == true);
        $this->assertTrue(round($investor->fresh()->vault) == round($vault + $vaultIncrement));
    }


    /**
     * @group Maintenance
     *  @author Keania
     *  * 
     * @return void
     */
    public function testMonthlyInvestorsGetPaid()
    {
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=>true
        ]);
         // set loan as active
         $plan->loan->update(['status'=>'1']);
        $investor = $plan->loan->loanRequest->investors()->first();
        $investor->update(['payback_cycle'=>'monthly']);

        $oldvault = $investor->vault;
        $vaultIncrement  = refresh_vault($investor,$plan);

        $this->investorPayer->issueRepaymentProcess('monthly');

        $refreshVault = $investor->refresh()->vault;
       
        $newVault = $oldvault + $vaultIncrement;
       
        $this->assertTrue($plan->fresh()->paid_out == true);
        $this->assertTrue(round($refreshVault) == round($newVault));
        
    }

    
}
