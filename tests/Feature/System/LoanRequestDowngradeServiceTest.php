<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\Users\LoanUpgradeNotification;
use App\Services\LoanRequestUpgradeService;
use Illuminate\Support\Facades\Notification;

class LoanRequestDowngradeServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    
    public function testALoanCanBeDowngraded()
    {
    // The downgrade service
    $downgradeService = new LoanRequestUpgradeService();
    

    // First we need a loan
    $loan = factory(\App\Models\Loan::class)->create(['duration'=> 3]);

    // Get loan owner
    $user = $loan->user;

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

    $canUpgrade = $downgradeService->checkLoanRequiresUpgrade($loan);
    // Assert it can be upgrade
    $this->assertTrue($canUpgrade);

    Notification::fake();

    // Upgrade works
    $downgradeService->upgradeUser($loan);

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

    //Test if user can be downgraded after upgrade
    $canDowngrade = $downgradeService->checkLoanRequiresDowngrade($loan);
    // Assert it can be upgrade
    $this->assertTrue($canDowngrade);
    // Downgrade works
    $downgradeService->downgradeUser($loan);   

    //User level decreased by 20
    $expectedLevel = $expectedLevel - 20;
    $user->refresh();
    $this->assertTrue($user->salary_percentage == $expectedLevel);

    // Datebase has new loanreqeuest level
    $this->assertDatabaseHas('loan_request_levels', [
        'cancelled' => 1
    ]);

}
    }

