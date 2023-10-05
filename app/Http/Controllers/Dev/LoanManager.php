<?php

namespace App\Http\Controllers\Dev;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Unicredit\Collection\CollectionService;

use App\Models\Loan;
use App\Models\Employer;
use App\Models\LoanRequest;

use App\Helpers\Constants;

use PDF, Mail;
use App\Models\RepaymentPlan;
use App\Mail\PaymentDue;
use App\Helpers\FinanceHandler;
use DB;


class LoanManager extends Controller
{
    public function fix(Request $request, FinanceHandler $financeHandler)
    {
        $loanRequest = LoanRequest::whereId($request->request_id)->first();
        
        if (!$loanRequest) return 'LR not found';
        
        $user = $loanRequest->user;
        $loanFunds = $loanRequest->funds;
        
        DB::beginTransaction();
        
        try {
            foreach($loanFunds as $loanFund) {
                $investor = $loanFund->investor;
                //update bidders wallet balance
                
                $code = config('unicredit.flow')['loan_fund_rvsl'];
                $financeHandler->handleDouble(
                    $user, $investor, $loanFund->amount, $loanRequest, 'ETW', $code
                );
                
                $loanFund->update(['status' => 3]);
            }
            
            //update loan request
            $loanRequest->update([
                'status' => 3
            ]);  
            
            if ($loan = $loanRequest->loan) {
                // Cancel loan
                $loan->update(['status' => 4]);
            }
            
            DB::commit();
            
            return 'Done';
            
        } catch(\Exeception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
