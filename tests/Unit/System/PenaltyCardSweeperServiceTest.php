<?php

namespace Tests\Unit\System;

use Mockery;
use Tests\TestCase;
use GuzzleHttp\Client;
use App\Models\PenaltySweep;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use App\Paystack\PaystackService;
use GuzzleHttp\Handler\MockHandler;
use App\Unicredit\Logs\DatabaseLogger;
use App\Services\PenaltyCardSweepService;
use App\Unicredit\Collection\CardService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\utilities\HttpTestResponseFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenaltyCardSweeperServiceTest extends TestCase
{
    
    use RefreshDatabase;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->responseProvider = new HttpTestResponseFactory($type = 'paystack-charge-test');
        $this->paystackService = Mockery::mock(PaystackService::class)
                ->shouldAllowMockingProtectedMethods();
                
        $this->sweeperService = new PenaltyCardSweepService($this->paystackService);
    }


    
    /**
     * Here we test that penalty sweeps works as expected
     * @author Keania
     * @group Maintenance
     * @return void
     */
    public function testSweepPenaltyLoansWorks()
    {

        list($loan, $amount, $user) = $this->setupData();
       
        $response = $this->responseProvider->createResponse('success');

        $this->paystackService->shouldReceive('getResponse')
            ->andReturn($response);

        $this->paystackService->shouldReceive('setHttpResponse')
            ->andReturnNull();
        Notification::fake();
        $this->sweeperService->sweepLoan($loan);

        // Ensure the user wallet has dropped
        $diff = 1/8 * $amount;
        $currentAmount = -$amount + $diff;

        $user->refresh();

        $this->assertDatabaseHas('penalty_sweeps', [
            'loan_id'=> $loan->id,
            'user_id'=> $loan->user->id,
            'amount'=> $diff
        ]);

        $this->assertTrue($currentAmount == $user->loan_wallet);

        // Ensure there is loan wallet transaction in the database showing credit
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'amount'=> $diff,
            'loan_id'=> $loan->id
        ]);


    }


    /**
     * Here we test that penalty sweeps works as expected
     * @author Keania
     * @group Maintenance
     * @return void
     */
    public function testSweepPenaltyLoansFailsOnChargeFailure()
    {

        list($loan, $amount, $user) = $this->setupData();
       
        $response = $this->responseProvider->createResponse('failed');

        $this->paystackService->shouldReceive('getResponse')
            ->andReturn($response);

        $this->paystackService->shouldReceive('setHttpResponse')
            ->andReturnNull();

        $this->sweeperService->sweepLoan($loan);

        $user->refresh();

        // Ensure that the user wallet remained same
        $this->assertTrue($user->loan_wallet == -$amount);
    }



    public function setupData()
    {
        $billingCard = factory(\App\Models\BillingCard::class)->create();

        $user = $billingCard->user;

        $amount = 4000;

        $user->update(['loan_wallet'=>-$amount]);

        $loan_wallet = $user->loan_wallet;

        $loan = factory(\App\Models\Loan::class)->create(['user_id'=> $user->id, 'is_penalized'=>true]);

        return [$loan, $amount, $user];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
