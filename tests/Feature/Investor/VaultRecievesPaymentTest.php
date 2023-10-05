<?php

namespace Tests\Feature\Investor;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VaultRecievesPaymentTest extends TestCase
{
    /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testInvestorVaultRecievesRepayments()
    {
        // This will create everything needed up to the repayment stage
        $plan = factory(\App\Models\RepaymentPlan::class)->create([
            'status'=>true
        ]);
        // make the loan active
        $plan->loan->update(['status'=>'1']);
        $vault = $plan->loan->loanRequest->investors()->first()->vault;
        
      
        \Artisan::call('investors:settle');
        //assert that the vault column has increased
        $newVault =  $plan->loan->loanRequest->investors()->first()->vault;
       
        $this->assertTrue($newVault > $vault);
        
    }
}
