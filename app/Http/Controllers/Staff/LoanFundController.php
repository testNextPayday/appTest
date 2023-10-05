<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\LoanRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\LoanFund;
use App\Models\Investor;
use App\Notifications\Users\LoanFundedNotification;
use Auth, DB, Validator;
use App\Traits\EncryptDecrypt;
use App\Helpers\FinanceHandler;

class LoanFundController extends Controller
{
    use EncryptDecrypt;
    
    public function index()
    {
        $staff = Auth::guard('staff')->user();
        $loanFunds = $staff->funds()->whereNull('original_id')->get();
        return view('staff.loan_funds', compact('staff', 'loanFunds'));
    }
    
    public function acquired()
    {
        $staff = Auth::guard('staff')->user();
        $loanFunds = $staff->funds()->whereNotNull('original_id')->get();
        return view('staff.loan_funds_acquired', compact('staff', 'loanFunds'));
    }
    
    public function viewFund($loan_id)
    {
        $loan_id = $this->basicDecrypt($loan_id);  
        $loan = LoanFund::find($loan_id);
        if(!$loan) abort(404);
        $fundFraction = 0;
        $repaymentPlans = [];
        $loaneeLoan = null;
        $currentValue = $loan->amount;
        if($loan->status > 1) {
            $loaneeLoan = $loan->loanRequest->loan;
            $totalCollectedByLoanee = $loaneeLoan->amount;
            $fundFraction = $loan->amount/$totalCollectedByLoanee;
            $repaymentPlans = $loaneeLoan->repaymentPlans;
            $currentValue = $fundFraction * ($loaneeLoan->repaymentPlans()->whereStatus(false)->sum('interest') + 
                $loaneeLoan->repaymentPlans()->whereStatus(false)->sum('principal'));
        }
        return view('staff.loan_fund', compact('loan', 'fundFraction', 'repaymentPlans', 'loaneeLoan', 'currentValue'));
    }
    
    public function fundLoan(Request $request, FinanceHandler $financeHandler)
    {
       
            try {

                DB::beginTransaction();

                $validationRules = [
                    'fund_percentage' => 'required',
                    'investor_id' => 'required|min:1'
                ];
                
                $validator = Validator::make($request->all(), $validationRules);

                if ($validator->fails()) {
                    throw new \InvalidArgumentException('Please supply fund percentage and account');
                }
                
                $loanRequest = LoanRequest::find($request->loan_request_id);
                
                if (!$loanRequest) 
                    throw new \InvalidArgumentException('Loan Request Not Found');
                
                if ($loanRequest->percentage_left < 1) {
                    throw new \InvalidArgumentException('Sorry, this loan has been fully funded');
                }
                
                if ($loanRequest->percentage_left < $request->fund_percentage) {
                    throw new \InvalidArgumentException('You can only fund ' . $loanRequest->percentage_left . '% of this loan');
                }
                
                $amount = $loanRequest->amount * $request->fund_percentage / 100;
                $investor = Investor::find($request->investor_id);

                if ($investor->wallet < $amount) {
                    throw new \InvalidArgumentException('This account does not have enough money to make this bid');
                }
                    
                
                $data = [
                    'investor_id' => $investor->id,
                    'request_id' => $request->loan_request_id,
                    'amount' => $amount,
                    'percentage' => $request->fund_percentage,
                    'staff_fund' => true,
                    'staff_id' => Auth::id()
                ];

                $loanFund = LoanFund::create($data);

                $borrower = $loanRequest->user;

                $code = config('unicredit.flow')['loan_fund'];
                $financeHandler->handleSingle(
                    $investor, 'debit', $amount, $loanRequest, 'W', $code
                );

                $percentageLeft = $loanRequest->percentage_left - $request->fund_percentage;

                $updates = ['percentage_left'=>$percentageLeft];

                if ($percentageLeft <= 0) {
                    $updates['status'] = 4;
                }
                        
                $loanRequest->update(
                    $updates
                );

                DB::commit();
                
            
            // we can now finally send notifications
            //sms and emails to bidder and request owner
            //$investor->notify(new LoanFundedNotification($loanFund));
            //$borrower->notify(new LoanFundedNotification($loanFund));

            } catch(\Exception $e) {

                DB::rollback();
                return response()->json(
                    ['status' => 0, 'message' => $e->getMessage()], 200
                );
            }
            
            
            return response()->json(['status' => 1], 200);
        
    }
    
    public function placeOnTransfer(Request $request)
    {
        $validationRules = [
            'amount' => 'required',
        ];
        
        $validator = Validator::make($request->all(), $validationRules);
        if($validator->fails()) {
            return response()->json(['status' => 0, 'message' => 'Please supply sale amount'], 200);
        }
        
        $loan = LoanFund::find($request->loan_id);
        if(!$loan) 
            return response()->json(['status' => 0, 'message' => 'Loan not found'], 200);
        if($loan->update(['status' => 4, 'sale_amount' => $request->amount])) {
            //TODO: sms and emails to loan giver and receiver
            return response()->json(['status' => 1, 'loan' => $loan], 200);
        }
        return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);
    }
    
    public function viewAcquiredLoan($loan_id)
    {
        $loan_id = $this->basicDecrypt($loan_id);  
        $loan = LoanFund::find($loan_id);
        if(!$loan) abort(404);
        $original_loan = $loan->original;
        while($original_loan->original_id != null) {
            $origianl_loan = $origianl_loan->original;
        }
        $loaneeLoan = $original_loan->loanRequest->loan;
        $totalCollectedByLoanee = $loaneeLoan->amount;
        $fundFraction = $original_loan->amount/$totalCollectedByLoanee;
        $repaymentPlans = $loaneeLoan->repaymentPlans;
        $currentValue = $fundFraction * ($loaneeLoan->repaymentPlans()->whereStatus(false)->sum('interest') + 
            $loaneeLoan->repaymentPlans()->whereStatus(false)->sum('principal'));
    
        return view('staff.loan_fund_acquired', compact('loan', 'original_loan', 'fundFraction', 'repaymentPlans', 'loaneeLoan', 'currentValue'));
    }
}
