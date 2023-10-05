<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LoanWalletTransaction;
use App\Services\LoanRepaymentService;
use App\Collection\CollectionFinanceHandler;

class LoanWalletTransactionController extends Controller
{
    //
    
    /**
     * Get wallet data
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     */
    public function getWalletData(Request $request, User $user)
    {
        $trnxs = $user->loanWalletTransactions()->where('status', 2)->get();

        return response()->json($trnxs);
    }
    
    /**
     * Store 
     *
     * @param  mixed $request
     * @param  mixed $loan
     * @return void
     */
    public function store(Request $request, Loan $loan, CollectionFinanceHandler $collecionFinanceService)
    {
        try {
            
            DB::beginTransaction();
           
            //$loan = Loan::whereReference($request->loan_ref)->first();
          
            $amount = $request->amount;

            $desc = $request->desc;

            $direction = $request->direction;

            $trnxDate = $request->date;

            $operation = $direction == 2 ? 'debit' : 'credit';

            $method = $request->method;

            $collecionFinanceService->logCollection($loan, $amount, $desc, $operation, $method);

            DB::commit();

            return response()->json('Success');

        }catch(\Exception $e) {

            DB::rollback();
            return response()->json($e->getMessage(), 422);
        }
    }


    
    /**
     * delete a repayment 
     *
     * @param  mixed $request
     * @return void
     */
    public function delete(Request $request, LoanRepaymentService $service)
    {
        try {
            
            DB::beginTransaction();
          
                $loanTrnx = LoanWalletTransaction::findorFail($request->id);
               
                if ($service->deleteLoanTransaction($loanTrnx)){

                    DB::commit();
                    return response()->json(['status'=> 1, 'message'=>'Transaction successfully deleted']);
                }
            DB::rollback();

            return response()->json(['status'=>0, 'message'=> 'Could not delete transaction']);

        }catch(\Exception $e) {

            DB::rollback();

            return response()->json($e->getMessage(), 422);
        }
    }


    public function update(Request $r)
    {
        // dd($r->all());

        try {
            
            // DB::beginTransaction();
          
                $loanTrnx = LoanWalletTransaction::findorFail($r->id);
                $loanTrnx->amount = $r->amount;

                $user = User::find($loanTrnx->user_id);

                if ($r->direction == '2') {
                    $user->update(['loan_wallet'=> $user->loan_wallet - $r->amount]);
                }else {
                    $user->update(['loan_wallet'=> $user->loan_wallet + $r->amount]);
                }

                $loanTrnx->description = $r->desc;
    
                $loanTrnx->direction = $r->direction;
    
                // $loanTrnx->created_at = $r->date;
    
                // $loanTrnx->direction = $direction ; //  == 2 ? 'debit' : 'credit';
    
                $method = $r->method;
    
                // $collecionFinanceService->logCollection($loan, $amount, $desc, $operation, $method);
    
                $loanTrnx->save();
               
                
            // DB::rollback();

            return response()->json(['status'=>0, 'message'=> 'Loan wallet transaction updated successfully']);

        }catch(\Exception $e) {

            // DB::rollback();

            return response()->json($e->getMessage(), 422);
        }
    }
}
