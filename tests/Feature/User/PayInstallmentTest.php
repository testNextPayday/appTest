<?php

namespace Tests\Feature\User;

use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\utilities\HttpTestResponseFactory;
use App\Services\TransactionVerificationService;
use App\Services\InstallmentPaymentVerifyService;
use Illuminate\Foundation\Testing\RefreshDatabase;


class PayInstallmentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->responseProvider = new HttpTestResponseFactory($type = 'paystack-charge-test');

    }


    /**
     * Checks that the verify payment used by the Installment payment works
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testVerifyTransactionWorks()
    {
        $response = $this->responseProvider->createResponse('success');
    
        // // Mocking the transaction verification service
        $verifyService = Mockery::mock(TransactionVerificationService::class)
                    ->shouldAllowMockingProtectedMethods();

        $verifyService->shouldReceive('verificationSuccessful')->andReturn(true);

        $verifyService->shouldReceive('verifyTransaction')->andReturn($response);

        $user = factory(\App\Models\User::class)->create();

        $data = ['reference'=> $response['data']['reference']];

        $this->signIn($user);

        $installmentVerify = new InstallmentPaymentVerifyService($verifyService);

        $response = $installmentVerify->verifyInstallment($data);

        $this->assertTrue($response['status'] == 1);

        $this->assertDatabaseHas('gateway_transactions', [
            'owner_id'=> $user->id,
            'owner_type'=> get_class($user),
            'reference'=> $data['reference']
        ]);

    }
    
    /**
     * Test update plans works
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testUpdatePlansWorks()
    {
        $user = factory(\App\Models\User::class)->create();

        $plan = factory(\App\Models\RepaymentPlan::class)->create();

        $loan = $plan->loan;

        $loan->update(['user_id'=>$user->id, 'status'=> '1']);

        $route = route('users.update-repaymentplans');

        $data = [ 'paymentIds'=>[$plan->id]];

        $response = $this->actingAs($user)->post($route, $data);
       
        $response->assertStatus(200);

        // Assert there exists a loan wallet transaction entering
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'loan_id'=> $loan->id,
            'plan_id'=> $plan->id,
            'amount'=> $plan->total_amount,
            'direction'=> 1
        ]);

        // Assert there exists a loan wallet transaction leaving
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'loan_id'=> $loan->id,
            'plan_id'=> $plan->id,
            'amount'=> $plan->total_amount,
            'direction'=> 2
        ]);
    }
    
    /**
     * Tears Down Mockery
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();
    }
}
