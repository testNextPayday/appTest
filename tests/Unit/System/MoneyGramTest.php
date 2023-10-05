<?php

namespace Tests\Unit\System;

use Mockery;
use Tests\TestCase;
use GuzzleHttp\Client;
use App\Models\BankDetail;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use App\Unicredit\Managers\MoneyGram;
use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\utilities\HttpTestResponseFactory;
use App\Unicredit\Payments\PaystackMoneySender;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoneyGramTest extends TestCase
{


    public function setUp(): void
    {
        parent::setUp();

        $this->httpResponseFactory = new HttpTestResponseFactory($type = 'uses-moneysender-test');
    }


    public function usePaystackSetup()
    {

        $this->channel = Mockery::mock(PaystackMoneySender::class);

        $dbLogger = new DatabaseLogger();
        
        $this->moneyGram = new MoneyGram($this->channel, $dbLogger);

    }

    /**
     * @group Maintenance
     * 
     * @author Keania
     * 
     */
    public function testMoneyGramPaystackTransfersSuccess()
    {

        $this->usePaystackSetup();

        $this->channel->shouldReceive('createTransfer')->andReturn($this->httpResponseFactory->createResponse('success-transfer'));

        $this->channel->shouldReceive('createRecipient')->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('verifyTransfer')->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));

        $loan = factory(\App\Models\Loan::class)->create();

        $data = [
            'amount'=>300,
            'type'=>$loan->reference.' Loan Disbursement',
            'link'=> $loan,
            'reference'=>generateHash()
        ];

        $bankDetails = factory(\App\Models\BankDetail::class)->create();

        $response = $this->moneyGram->makeTransfer($bankDetails, $data);
       
        $this->assertTrue($response["status"] == "success");
       
        $bankDetails->refresh();

        $newRecord = $bankDetails->gatewayRecords->sortBydesc('created_at')->first();

        $this->assertTrue($newRecord->pay_status === 1);
        
    }



    /**
     * @group Maintenance
     * 
     * @author Keania
     * 
     */
    public function testMoneyGramPaystackTransfersFails()
    {

        $this->usePaystackSetup();

        $this->channel->shouldReceive('createTransfer')->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));

        $this->channel->shouldReceive('createRecipient')->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('verifyTransfer')->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));

        $loan = factory(\App\Models\Loan::class)->create();
        
        $data = [
            'amount'=> 300,
            'type'=> $loan->reference.' Loan Disbursement',
            'link'=> $loan,
            'reference'=>generateHash()
        ];

        $bankDetails = factory(\App\Models\BankDetail::class)->create();

        $response = $this->moneyGram->makeTransfer($bankDetails, $data);

        $bankDetails->refresh();

        $newRecord = $bankDetails->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($newRecord->pay_status === 0);
        
    }


     /**
     * @group Maintenance
     * 
     * @author Keania
     * 
     * @expectedException \App\Unicredit\Exceptions\MoneyGramRecipientCreationException
     */
    public function testMoneyGramPaystackTransfersRecipientFails()
    {

        $this->usePaystackSetup();

        $this->channel->shouldReceive('createTransfer')->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));

        $this->channel->shouldReceive('createRecipient')->andReturn($this->httpResponseFactory->createResponse('failed-recipient'));

        $this->channel->shouldReceive('verifyTransfer')->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));

        $data = ['amount'=>300];

        $bankDetails = factory(\App\Models\BankDetail::class)->create();

        $response = $this->moneyGram->makeTransfer($bankDetails,$data);

        

        
        
    }


    public function tearDown(): void
    {
        Mockery::close();
    }
}
