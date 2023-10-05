<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use App\Models\Settings;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use App\Unicredit\Managers\PortfolioManager;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PortfolioManagerTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->portfolioManager = new PortfolioManager(new FinanceHandler(new TransactionLogger));
    }

    /**
     * @group Maintenance
     * @author Keania
     * @expectedException \App\Unicredit\Exceptions\NoSettingsAvailableException
     * @return void
     */
    public function testPortFolioManagerThrowsNoSettingsAvailableException()
    {
        $this->portfolioManager->issuePortfolioManagementCollection();

    }


    /**
     * @group Maintenance
     * @author Keania
     * 
     * @return void
     */
    public function testPortFolioManagerCollectFeesFromWallet()
    {
        //create an investor
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>2000000,
        ]);
        $code = config('unicredit.flow')['portfolio_management_fee'];
        //assert that the investor has no transaction 
        $this->assertTrue($investor->transactions->where('code',$code)->isEmpty());
        // set the settings
        $settings = Settings::create([
            'name'=>'Investor Portfolio Management Fee',
            'slug'=>'portfolio_management_percentage_fee',
            'value'=>'5'
        ]);
        $portfolioManagementFee = ($settings->value/100) * $investor->wallet;
        $shouldBeWallet= round($investor->wallet - $portfolioManagementFee,2);
        $this->portfolioManager->issuePortfolioManagementCollection();
        $this->assertTrue($investor->fresh()->wallet == $shouldBeWallet);
        $this->assertTrue($investor->fresh()->transactions->where('code',$code)->isNotEmpty());
       
    }


    /**
     * @group Maintenance
     * @author Keania
     * 
     * @return void
     */
    public function testPortFolioManagerCollectFeesFromUnpaidPlans()
    {
        
        $plan = factory(\App\Models\RepaymentPlan::class)->create();
        $investor = $plan->loan->loanRequest->investors()->first();
        $code = config('unicredit.flow')['portfolio_management_fee'];
        
        // set the settings
        $settings = Settings::create([
            'name'=>'Investor Portfolio Management Fee',
            'slug'=>'portfolio_management_percentage_fee',
            'value'=>'5'
        ]);
        $portfolioManagementFee = ($settings->value/100) * $investor->portfolioSize();
       // $shouldBePortfolioSize = round($investor->portfolioSize()- $portfolioManagementFee,2);

        $this->portfolioManager->issuePortfolioManagementCollection();
        $investorTransaction = $investor->fresh()->transactions->where('code',$code);
        $this->assertTrue($investorTransaction->isNotEmpty());
        $this->assertTrue($investorTransaction->first()->amount == $portfolioManagementFee);
       
    }


     /**
     * @group Maintenance
     * @author Keania
     * 
     * @return void
     */
    public function testPortFolioManagerCollectGivesNegativeWithLowWalletBalance()
    {
        
        $plan = factory(\App\Models\RepaymentPlan::class)->create();
        $investor = $plan->loan->loanRequest->investors()->first();
        $code = config('unicredit.flow')['portfolio_management_fee'];
        
        // set wallet balance to zero
        $investor->update([
            'wallet'=>0.0
        ]);

        // set the settings
        $settings = Settings::create([
            'name'=>'Investor Portfolio Management Fee',
            'slug'=>'portfolio_management_percentage_fee',
            'value'=>'5'
        ]);
        $portfolioManagementFee = ($settings->value/100) * $investor->portfolioSize();
      
        //issue collection    
        $this->portfolioManager->issuePortfolioManagementCollection();
       
        $this->assertTrue($investor->fresh()->wallet < 0);
        $investorTransaction = $investor->fresh()->transactions->where('code',$code);
        $this->assertTrue($investorTransaction->isNotEmpty());
        $this->assertTrue($investorTransaction->first()->amount == $portfolioManagementFee);
       
    }


    /**
     * @group Maintenance
     * @author Keania
     * 
     * @return void
     */
    public function testPortFolioManagerThrowsZeroPortfolioBalanceException()
    {
        
        
         //create an investor with 0 balance
         $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>0.0,
        ]);
        $code = config('unicredit.flow')['portfolio_management_fee'];
        
       

        // set the settings
        $settings = Settings::create([
            'name'=>'Investor Portfolio Management Fee',
            'slug'=>'portfolio_management_percentage_fee',
            'value'=>'5'
        ]);
        $portfolioManagementFee = ($settings->value/100) * $investor->portfolioSize();
      
        //issue collection    
        $this->portfolioManager->issuePortfolioManagementCollection();
       
        $this->assertDatabaseHas('logs',[
            'resource_id'=>$investor->id,
            'resource_type'=>get_class($investor),
            'title'=>'PortFolio Management',
            'description'=>' Attempted Portfolio Management Collection',
            'status'=>0
        ]);
       
    }




}
