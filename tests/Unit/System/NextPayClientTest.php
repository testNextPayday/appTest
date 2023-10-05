<?php

namespace Tests\Unit\System;

use Mockery;
use Tests\TestCase;
use App\Models\Bill;
use App\Models\Staff;
use GuzzleHttp\Client;
use App\Models\BankDetail;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use App\Unicredit\Managers\MoneyGram;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Payments\NextPayClient;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\utilities\HttpTestResponseFactory;
use App\Unicredit\Payments\PaystackMoneySender;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NextPayClientTest extends TestCase
{
    /** Testing the class that will pay salaries and bills */


    public function setUp(): void
    {
        parent::setUp();

        $this->httpResponseFactory = new HttpTestResponseFactory($type = 'uses-moneysender-test');

        $financeHandler = new FinanceHandler(new TransactionLogger);

        $this->channel = Mockery::mock(PaystackMoneySender::class);

        $dbLogger = new DatabaseLogger();

        $moneyGram = new MoneyGram($this->channel, $dbLogger);


        $this->paymentClient = new NextPayClient($financeHandler, $moneyGram);
        
    }
    /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testNextPayClientPayBillsSucceed()
    {
        $bill = factory(\App\Models\Bill::class)->create();

        $bill->addBeneficiaryAccount(
            $data = factory(\App\Models\BankDetail::class)->make()->toArray()
        );

        $this->channel->shouldReceive('createRecipient')
        ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
        ->andReturn($this->httpResponseFactory->createResponse('success-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
        ->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));
        
        $this->paymentClient->payBill($bill);

        $gatewayT = $bill->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 1);
    }


    public function testNextPayClientPayBillsFails()
    {

        $bill = factory(\App\Models\Bill::class)->create();

        $bill->addBeneficiaryAccount(
            $data = factory(\App\Models\BankDetail::class
        )->make()->toArray());

        $this->channel->shouldReceive('createRecipient')
        ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
        ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
        ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));
        
        $this->paymentClient->payBill($bill);

        $gatewayT = $bill->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);

    }

    public function testNextPayClientPayBillsPends()
    {

        $bill = factory(\App\Models\Bill::class)->create();

        $bill->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());

        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('pending-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                
                ->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));
        
        $this->paymentClient->payBill($bill);

        $gatewayT = $bill->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);

    }

    public function testNextPayClientPaySalarySuccess()
    {

        $staff = factory(\App\Models\Staff::class)->create();

        $staff->update(['salary'=>50000,'is_active'=>true]);

        $staff->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());

        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('success-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                
                ->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));
        
        $this->paymentClient->payStaffSalary($staff);

        $gatewayT = $staff->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 1);
    }


    public function testNextPayClientPaySalaryFails()
    {

        $staff = factory(\App\Models\Staff::class)->create();

        $staff->update(['salary'=>50000,'is_active'=>true]);

        $staff->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());

        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));
        
        $this->paymentClient->payStaffSalary($staff);

        $gatewayT = $staff->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);
    }


    public function testNextPayClientPaySalaryPends()
    {

        $staff = factory(\App\Models\Staff::class)->create();


        $staff->update(['salary'=>50000,'is_active'=>true]);

        $staff->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());

        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('pending-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));
        
        $this->paymentClient->payStaffSalary($staff);

        $gatewayT = $staff->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);
    }



    public function testNextPayClientPayRefundSuccess()
    {

        $refund = factory(\App\Models\Refund::class)->create(['status'=>1]);

        
        $refund->user->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());


        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('success-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                
                ->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));
        
        $this->paymentClient->payRefund($refund);

        $gatewayT = $refund->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 1);
    }


    public function testNextPayClientPayRefundFails()
    {

        $refund = factory(\App\Models\Refund::class)->create(['status'=>1]);

        $refund->user->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());


        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                
                ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));
        
        $this->paymentClient->payRefund($refund);

        $gatewayT = $refund->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);
    }


    public function testNextPayClientPayRefundPends()
    {

        $refund = factory(\App\Models\Refund::class)->create(['status'=>1]);

        $refund->user->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());


        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('pending-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                
                ->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));
        
        $this->paymentClient->payRefund($refund);

        $gatewayT = $refund->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);
    }



    public function testNextPayClientPushMoneySuccess()
    {

        $loan = factory(\App\Models\Loan::class)->create(['status'=>1]);

        
        $loan->user->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());


        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('success-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                
                ->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));
        
        $this->paymentClient->pushMoney($loan);

        $gatewayT = $loan->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 1);
    }


    public function testNextPayClientPushMoneyPends()
    {

        $loan = factory(\App\Models\Loan::class)->create(['status'=>1]);

        
        $loan->user->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());


        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('pending-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                
                ->andReturn($this->httpResponseFactory->createResponse('verify-transfer'));
        
        $this->paymentClient->pushMoney($loan);

        $gatewayT = $loan->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);
    }

    public function testNextPayClientPushMoneyFails()
    {

        $loan = factory(\App\Models\Loan::class)->create(['status'=>1]);

        
        $loan->user->addBeneficiaryAccount($data = factory(\App\Models\BankDetail::class)->make()->toArray());


        $this->channel->shouldReceive('createRecipient')
            ->andReturn($this->httpResponseFactory->createResponse('success-recipient'));

        $this->channel->shouldReceive('createTransfer')
                ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));

        $this->channel->shouldReceive('verifyTransfer')
                
                ->andReturn($this->httpResponseFactory->createResponse('failed-transfer'));
        
        $this->paymentClient->pushMoney($loan);

        $gatewayT = $loan->gatewayRecords->sortByDesc('created_at')->first();

        $this->assertTrue($gatewayT->pay_status == 0);
    }



}
