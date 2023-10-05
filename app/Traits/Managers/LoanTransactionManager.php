<?php
namespace App\Traits\Managers;

use App\Models\LoanTransaction;

use App\Models\Loan;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use App\Models\RepaymentPlan;
use Illuminate\Http\Request;

trait LoanTransactionManager 
{

    public  function addLoanTransaction($request)
    {
       
            $loan = Loan::find($request->loan_id);
            $plan = $loan->repaymentPlans->where('status', 1)->last();
            if(!$plan) throw new \Exception('cannot add transactions to loans without confirmed repayments');
            $user = $loan->user;
            $tranx = $this->createTranx($request,$plan);
            //$this->LogTranx($user,$plan,$tranx);
            $this->update($plan,$tranx,'wallet_balance',true);
          
       
    }

    public function LogTranx($user,$plan,$tranx)
    {
        $handler = new FinanceHandler(new TransactionLogger);
        $code = config('unicredit.flow')['loan_transaction'];
        $type = $tranx->type == 1 ? 'debit' : 'credit';
        $handler->handleSingle(
            $user,
            $type,
            $tranx->amount,
            $plan,
            'W',
            $code
        );
     }

    public function createTranx(Request $request,RepaymentPlan $plan)
    {
       
       
        $balance = ($request->type == 1) ? $plan->wallet_balance - $request->amount : $plan->wallet_balance + $request->amount;
         return LoanTransaction::create([
            'name' => $request->transaction_name,
            'type' => $request->type,
            'loan_id' => $request->loan_id,
            'plan_id' => $plan->id,
            'description' => $request->description,
            'amount' => $request->amount,
            'wallet_balance' => $balance,
        ]);
    }


    public function update($entity,$tranx,$column,$ch = false)
    {
        $wallet  = $entity->{$column};
        ($tranx->type == 1) ? $entity->update([$column=>$wallet - $tranx->amount]) : $entity->update([$column=>$wallet + $tranx->amount]);
        if($ch){
            $entity->update(['wallet_changed'=>true]);
        }
        return true;
    }

   
}
?>