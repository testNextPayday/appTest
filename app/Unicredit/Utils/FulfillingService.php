<?php
namespace App\Unicredit\Utils;

use App\Helpers\Constants;
use App\Helpers\FinanceHandler;
use App\Models\LoanTransaction;
use App\Helpers\TransactionLogger;
use App\Remita\DDM\MandateManager;
use App\Unicredit\Logs\DatabaseLogger;
use App\Services\LoanRequestUpgradeService;



class FulfillingService extends LoanRequestUpgradeService

{
    public function __construct()
    {        
        $this->dbLogger = new DatabaseLogger();
        $this->financeHandler = new FinanceHandler(new TransactionLogger);        
    }

    public function fulfill($loan)
    {   
        $loan->update(
            [
                'status' => '2',
                'fulfilled_at'=>now()->toDateTimeString()
            ]
        );
        $this->stopMandate($loan);
        $this->handlePlanBalance($loan);

        if($this->checkLoanRequiresUpgrade($loan) == true){
            $this->upgradeUser($loan);
        }               
    }

    private function handlePlanBalance($loan)
    {
        $plan = $loan->repaymentPlans->last();
        $amount = $plan->wallet_balance;
        if ($amount > 0) {
            $tranx = $this->recordTransaction($loan, $amount);
            $user = $loan->user;
            $this->log($user, $plan, $tranx);
            $plan->update(['wallet_balance'=>0]);
        }
    }

    private function stopMandate($loan)
    {
        $response = (new MandateManager())->stopMandate($loan);
        // log the mandate activity
        (new DatabaseLogger())->log($loan, $response, 'mandate-stop');
        $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 1);
    }


    private function recordTransaction($loan,$amount)
    {
        $data = $this->buildTransactionData($loan, $amount);
        return LoanTransaction::create($data);
    }

    private function buildTransactionData($loan, $amount)
    {
        return [

            'name'=> ' Moving repayment balance to user wallet',
            'type'=>' Debit',
            'loan_id'=>$loan->id,
            'description'=> ' Moving repayment balance to wallet',
            'amount'=>$amount,
            'wallet_balance'=>$amount,
        ];
    }

    private function log($user, $plan, $tranx)
    {       
        $code = config('unicredit.flow')['loan_transaction'];       
        $type = $tranx->type == 1 ? 'debit' : 'credit';
        $this->financeHandler->handleSingle(
            $user,
            $type,
            $tranx->amount,
            $plan,
            'W',
            $code
        );
     }
}
?>