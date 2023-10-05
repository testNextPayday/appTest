<?php

namespace Tests\Unit\System;

use Mockery;
use Tests\TestCase;
use App\Models\BankDetail;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use App\Unicredit\Managers\MoneyGram;
use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\utilities\HttpTestResponseFactory;
use App\Unicredit\Payments\WithdrawalHandler;
use App\Unicredit\Payments\PaystackMoneySender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Console\Commands\Payments\CheckTransactionStatus;

class WithdrawalHandlerTest extends TestCase
{
    /**
     * The goal of this test is to withdrawals placed can get to bank accounts
     */

    public function setUp(): void
     {

        parent::setUp();

        $this->httpResponseFactory = new HttpTestResponseFactory($type = 'uses-moneysender-test');

        $financeHandler = new FinanceHandler(new TransactionLogger);

        $this->channel = Mockery::mock(PaystackMoneySender::class);

        $dbLogger = new DatabaseLogger();

        $moneyGram = new MoneyGram($this->channel, $dbLogger);

        $this->statusCheck = new CheckTransactionStatus($this->channel, $dbLogger);


        $this->withdrawalHandler = new WithdrawalHandler($financeHandler, $moneyGram);
        
     }

        
    /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testWithdrawalPasses()
    {
        $bankDetails = factory(\App\Models\BankDetail::class)->create();
       
        $owner = $bankDetails->owner;

        $data = ['amount'=> 300,'type'=>'Withdrawal Request'];

        $request = factory(\App\Models\WithdrawalRequest::class)->create([
            'requester_id'=> $owner->id,
            'requester_type'=> get_class($owner),
            'amount'=> $data['amount']
        ]);

        $expectedBalance = $owner->wallet - $data['amount'];

        $successRecipient = $this->httpResponseFactory->createResponse('success-recipient');
        $this->channel->shouldReceive('createRecipient')
                        ->andReturn($successRecipient);
        
        $successTransfer = $this->httpResponseFactory->createResponse('success-transfer');
        $this->channel->shouldReceive('createTransfer')
                        ->andReturn($successTransfer);
        
        $verifyTransfer  = $this->httpResponseFactory->createResponse('verify-transfer');
        $this->channel->shouldReceive('verifyTransfer')
                        ->andReturn($verifyTransfer);
        
        $this->withdrawalHandler->handleRequest($request);

        // check that the owner of the bank details has an approved withdrawal request
        $owner->refresh();

        $bankDetails->refresh();

        $gatewayT = $request->gatewayRecords->sortByDesc('created_at')->first();
       
        // latest withdrawal
        $request->refresh();

        $this->statusCheck->checkPaymentStatus($gatewayT->reference);

        $this->assertTrue($request->status == 2);

        $this->assertTrue($gatewayT->pay_status == 1);

    }

    /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testWithdrawalBackendPasses()
    {
        $bankDetails = factory(\App\Models\BankDetail::class)->create();
       
        $owner = $bankDetails->owner;

        $data = ['amount'=> 300,'type'=>'Withdrawal Request'];

        $request = factory(\App\Models\WithdrawalRequest::class)->create([
            'requester_id'=> $owner->id,
            'requester_type'=> get_class($owner),
            'amount'=> $data['amount']
        ]);

        $expectedBalance = $owner->wallet - $data['amount'];

        $successRecipient = $this->httpResponseFactory->createResponse('success-recipient');
        $this->channel->shouldReceive('createRecipient')
                        ->andReturn($successRecipient);
        
        $successTransfer = $this->httpResponseFactory->createResponse('success-transfer');
        $this->channel->shouldReceive('createTransfer')
                        ->andReturn($successTransfer);
        
        $verifyTransfer  = $this->httpResponseFactory->createResponse('verify-transfer');
        $this->channel->shouldReceive('verifyTransfer')
                        ->andReturn($verifyTransfer);
        
        $this->withdrawalHandler->handleRequestBackend($request);

        // check that the owner of the bank details has an approved withdrawal request
        $owner->refresh();
       
        // latest withdrawal
        $request->refresh();

        $this->assertTrue($request->status == 2);

    }


         
    /**
     * @group Maintenance
     * @author Keania
     * 
     * @return void
     */
    public function testWithdrawalFails()
    {
        $bankDetails = factory(\App\Models\BankDetail::class)->create();
       
        $owner = $bankDetails->owner;

        $data = ['amount'=> 300,'type'=>'Withdrawal Request'];

        $request = factory(\App\Models\WithdrawalRequest::class)->create([
            'requester_id'=> $owner->id,
            'requester_type'=> get_class($owner),
            'amount'=> $data['amount']
        ]);

        $expectedBalance = $owner->wallet - $data['amount'];


        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
       
                ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));
        

        $this->withdrawalHandler->handleRequest($request);

        // check that the owner of the bank details has an approved withdrawal request
        $owner->refresh();

        $bankDetails->refresh();
       
        //$this->assertTrue($owner->wallet == $expectedBalance);

        // latest withdrawal
        $withdrawal = $owner->withdrawals->sortByDesc('created_at')->first();

        $this->assertTrue($withdrawal->status == '1');

        \Artisan::call('check:transaction');

        $gatewayT = $withdrawal->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);

    }
}
