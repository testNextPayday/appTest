<?php
/** 
 * This trait has a setUp method we wil be reusing for all our test
 */
namespace Tests\Unit\Traits;


use App\Models\RepaymentPlan;
use App\Unicredit\Managers\SettlementManager;

use Tests\utilities\HttpTestResponseFactory;

use App\Models\Loan;
use App\Models\User;
use Unicodeveloper\Paystack\Paystack;
use Mockery;




trait TestSettlementSetup
{

    /**
     * Setsup mock parameters for testing
     * @return void
     */
    public function setupTest()
    {
       
        $this->mock = Mockery::mock(Paystack::class);

        $settlementManager = new SettlementManager();

        $settlementManager->setPaymentHandler($this->mock);

        $this->app->instance(SettlementManager::class, $settlementManager);

        $this->httpResponseFactory = new HttpTestResponseFactory(
            $type='paystack-charge-test'
        );

        $this->user = factory(\App\Models\User::class)->create();

        //ensure users are verified
        $this->user->update(['email_verified'=>1]);

        $this->loan = factory(\App\Models\Loan::class)->create();

        $this->loan->update(['user_id'=> $this->user->id]);

        $this->loan->loanRequest->update(['user_id'=>$this->user->id]);

        $plans = factory(\App\Models\RepaymentPlan::class, 6)->create(
            ['loan_id'=>$this->loan->id]
        );

        $this->data = [
            'amount'=>$this->loan->settlement_amount * 100,
            'email'=>$this->user->email,
            'reference'=>'754372902eyrurwsg',
            'metadata'=>json_encode(['loan_reference'=>$this->loan->reference])
        ];
    }
}
?>