<?php

namespace App\Http\Controllers\Staff;

use Auth;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LoanTransaction;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Traits\Managers\LoanManager;
use App\Services\DissolveLoanService;
use App\Unicredit\Contracts\Models\ILoanRepository;

class LoanController extends Controller
{
    use LoanManager;

    public function __construct(ILoanRepository $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }
    
    public function index()
    {
        $loans = Auth::guard('staff')->user()->loans;
        return view('staff.loans.received', compact('loans'));
    }

    /**
     * Dissolves a loan 
     *
     * @param  mixed $request
     * @param  mixed $loan
     * @return void
     */
    public function dissolveLoan(Request $request, Loan $loan, DissolveLoanService $dissolveService)
    {
        try {

            DB::beginTransaction();
                $dissolveService->dissolveLoan($loan);
            DB::commit();

            return redirect()->back()->with('success', 'Loan has been dissolved successfully');

        }catch(\Exception $e) {
            
            DB::rollback();

            return redirect()->back()->with('failure', $e->getMessage());
        }
    }
    
    public function view($reference)
    {
        
        $loan = Loan::whereReference($reference)->first();
        if(!$loan)
            return redirect()->back()->with('failure', 'Loan does not exist');
        
        return view('staff.loans.show', compact('loan'));
    }

      public function pendingLoans()
    {
        $loans = Loan::where('status', "0")->latest()->get();
        return view('staff.loans.pending', compact('loans'));
    }
    public function fulfilledLoans(){

         $loans = $this->loanRepository->getFulfilledLoans();
        return view('staff.loans.fulfilled', compact('loans'));
    }

    public function managed()
    {
        $loans = $this->loanRepository->getActiveStatusLoans()->where('is_managed', true);
        return view('staff.loans.managed', compact('loans'));
    }
    public function activeLoans()
    {
        $loans = $this->loanRepository->getActiveStatusLoans()->where('is_managed', false);
        return view('staff.loans.active', compact('loans'));
    }
    public function eligibleTopUp(){
        $loans = $this->loanRepository->getEligibleTopup();
        return view('staff.loans.top_up', compact('loans'));
    }
    
    public function sweepableLoans()
    {
        $loans = $this->getSweepableLoans();
        return view('staff.loans.sweepable',compact('loans'));
    }
    
    public function receivedLoans()
    {
        $loans = Loan::latest()->paginate(20);
        return view('staff.loans_received', compact('loans'));
    }

    public function givenLoans()
    {
        $loans = LoanFund::latest()->paginate(20);
        return view('admin.loans_given', compact('loans'));
    }
    public function viewGivenLoan($loan_id)
    {
        $loan_id = $this->basicDecrypt($loan_id);  
        $loan = LoanFund::find($loan_id);
        if(!$loan) return redirect()->back()->with('failure', 'Loan not found');
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
    
    public function acquiredLoans()
    {
        $loans = LoanFund::whereNotNull('original_id')->latest()->paginate(20);
        return view('staff.loans_acquired', compact('loans'));
    }
    
    public function viewAcquiredLoan($loan_id)
    {
        $loan_id = $this->basicDecrypt($loan_id);  
        $loan = LoanFund::find($loan_id);
        if(!$loan) return redirect()->back()->with('failure', 'Loan not found');
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
    
        return view('staff.loan_acquired', compact('loan', 'original_loan', 'fundFraction', 'repaymentPlans', 'loaneeLoan', 'currentValue'));
    }
}
