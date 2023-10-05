<?php

namespace Tests\Feature\System;

use App\Notifications\Users\LoanUpgradeNotification;
use Tests\TestCase;
use App\Services\LoanRequestUpgradeService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanRequestUpgradeServiceTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test a loan can bee upgraded
     *  @group Maintenance
     *  @author Esther
     * @return void
     */
    public function testALoanCanBeUpgraded() 
    {

        // The upgrade service
        $upgradeService = new LoanRequestUpgradeService();

        // First we need a loan
        $loan = factory(\App\Models\Loan::class)->create(['duration'=> 3]);       

        // Get loan owner
        $user = $loan->user;

        //create employer for the user
        $employment = factory(\App\Models\Employment::class)->create([
            'user_id'=> $user->id
        ]);
        // enable upgrade on the employer
        $employment->employer->update(['upgrade_enabled' => 1]);

        //$this->assertTrue($user->salary_percentage == $expectedLevel);
        $previousLevel = $user->salary_percentage;
        // repayments that belong to the loan
        factory(\App\Models\RepaymentPlan::class)->create([
            'loan_id'=>$loan->id, 
            'payday'=> '2020-02-02',
            'date_paid'=> '2020-02-02',
            'status'=> 1
        ]);

        factory(\App\Models\RepaymentPlan::class)->create([
            'loan_id'=>$loan->id, 
            'payday'=> '2020-03-22',
            'date_paid'=> '2020-03-22',
            'status'=> 1
        ]);

        factory(\App\Models\RepaymentPlan::class)->create([
            'loan_id'=>$loan->id, 
            'payday'=> '2020-02-05',
            'date_paid'=> '2020-02-12',
            'status'=> 1
        ]);

        $canUpgrade = $upgradeService->checkLoanRequiresUpgrade($loan);
        // Assert it can be upgrade
        $this->assertTrue($canUpgrade);

        Notification::fake();

        // Upgrade works
        $upgradeService->upgradeUser($loan);

        //Notification was sent
        Notification::assertSentTo($user, LoanUpgradeNotification::class);

        //User level increased by 20
        $expectedLevel = $previousLevel + 20;
        $user->refresh();
        $this->assertTrue($user->salary_percentage == $expectedLevel);

        // Datebase has new loanreqeuest level
        $this->assertDatabaseHas('loan_request_levels', [
            'user_id'=> $user->id,
            'loan_id'=> $loan->id,
            'salary_percentage'=> 20
        ]);
                
    }
    
    /**
     *  Test on certain conditions loan will not upgrade
     *  @group Maintenance
     *  @author Esther
     *
     * @return void
     */
    public function testALoanCannotBeUpgraded()
    {
        // The upgrade service
        $upgradeService = new LoanRequestUpgradeService();

        // First we need a loan
        $loan = factory(\App\Models\Loan::class)->create(['duration'=> 3]);

        // Get loan owner
        $user = $loan->user;

        //create employer for the user
        $employment = factory(\App\Models\Employment::class)->create([
            'user_id'=> $user->id
        ]);
        // enable upgrade on the employer
        $employment->employer->update(['upgrade_enabled' => 1]);

        $previousLevel = $user->salary_percentage;

        // repayments that belong to the loan
        factory(\App\Models\RepaymentPlan::class)->create([
            'loan_id'=>$loan->id, 
            'payday'=> '2020-02-02',
            'date_paid'=> '2020-02-20',
            'status'=> 1
        ]);

        factory(\App\Models\RepaymentPlan::class)->create([
            'loan_id'=>$loan->id, 
            'payday'=> '2020-03-22',
            'date_paid'=> '2020-04-22',
            'status'=> 1
        ]);

        factory(\App\Models\RepaymentPlan::class)->create([
            'loan_id'=>$loan->id, 
            'payday'=> '2020-02-05',
            'date_paid'=> '2020-02-12',
            'status'=> 1
        ]);

        $canUpgrade = $upgradeService->checkLoanRequiresUpgrade($loan);
        // Assert it can be upgrade
        $this->assertFalse($canUpgrade);
}


    
}
