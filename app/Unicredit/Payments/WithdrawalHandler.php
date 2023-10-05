<?php
namespace App\Unicredit\Payments;

use Illuminate\Http\Request;
use App\Helpers\FinanceHandler;
use App\Models\WithdrawalRequest;
use App\Unicredit\Managers\MoneyGram;
use Illuminate\Database\Eloquent\Model;

class WithdrawalHandler

{
    protected $financeHandler;

    protected $withdrawer;

    protected $amount;

    protected $type = 'Withdrawal Request';

    public function __construct(FinanceHandler $financeHandler, MoneyGram $moneyGram)
    {

        $this->financeHandler = $financeHandler;

        $this->moneyGram = $moneyGram;

    }

    public function handleRequest(WithdrawalRequest $request)
    {

          
        $withdrawData =  [];

        $withdrawal = $request;

        $this->withdrawer = $withdrawal->requester;

        $bankDetails = $this->withdrawer->banks->last();

        if (! $bankDetails) {
            throw new \InvalidArgumentException('Recipient has no bank account for payment');
        }
     
        $withdrawData['amount'] = $withdrawal->amount;

        $withdrawData['type'] = $this->type;
        $withdrawData['link'] = $withdrawal; 

        $gatewayResponse = $this->moneyGram->makeTransfer($bankDetails, $withdrawData);
      
        $updates = [];

        $status = @$gatewayResponse['status'];
      
        // this will never update because the status will never be true
        // the cron job will update it once it works
        if( $status == 'success' || $status == 'otp' || $status == 'pending'){
            // update the withdrawal status to attended
          
           $updates['status'] = '2';
        }

        $withdrawal->update($updates);

        $this->deductFromEscrow($withdrawal);

        return $gatewayResponse;

        
    }

    
    /**
     * Handle a withdrawal without making transfers
     *
     * @param  mixed $request
     * @return void
     */
    public function handleRequestBackend(WithdrawalRequest $request)
    {

        $withdrawal = $request;

        $this->withdrawer = $withdrawal->requester;
      

        $withdrawal->update(['status'=> '2']);

        $this->deductFromEscrow($withdrawal);

       
        
    }


    


    // private function deductToEscrow($withdrawal)
    // {
    //     $code = config('unicredit.flow')['withdrawal'];

    //     $this->financeHandler->handleDouble(

    //         $this->withdrawer, $this->withdrawer, $this->amount , $withdrawal, 'WTE', $code
    //     );
    // }


    private function deductFromEscrow($withdrawal)
    {

        $code = config('unicredit.flow')['withdrawal_approval'];

        $this->financeHandler->handleSingle(
            $this->withdrawer, 'debit', $withdrawal->amount, $withdrawal, 'E', $code
        );

    }

    private function validateRequest($request)

    {
        // allows custom validation by the model involved
        if(method_exists($this->withdrawer,'validateWithdrawals')){

            return $this->withdrawer->validateWithdrawals($request);
        }

        throw new \BadMethodCallException(get_class($this->withdrawer." cannot validate withdrawals so cannot withdraw"));
       
        
    }
}
?>