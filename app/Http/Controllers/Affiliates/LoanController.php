<?php

namespace App\Http\Controllers\Affiliates;

use App\Models\Loan;
use App\Models\User;

use Illuminate\Http\Request;
use App\Models\LoanTransaction;
use App\Http\Controllers\Controller;
use App\Traits\Managers\LoanManager;
use App\Unicredit\Contracts\Models\ILoanRepository;

class LoanController extends Controller
{

    use LoanManager;
    
    public function __construct(ILoanRepository $loanRepository)
    {
        $this->loanRepository = $loanRepository;
        $this->middleware('auth:affiliate');    
    }
    
    public function view(Loan $loan)
    {
        $affiliate = auth('affiliate')->user();
         //$users = User::with(['employments'])->get();
        
        if (!$loan) {
            return redirect()->back()->with('failure', 'Loan not found');    
        }
        
        return view('affiliates.loans.show', compact('loan'));
    }

    public function fulfilledLoans(){

        $loans = $this->loanRepository->getFulfilledLoans();
        
        return view('affiliates.loans.fulfilled', compact('loans'));
    }

    public function activeLoans()
    {
        $loans  = $this->loanRepository->getActiveStatusLoans();

        return view('affiliates.loans.active', compact('loans'));
    }

    public function eligibleTopUp()
    {
        
        $loans = $this->loanRepository->getEligibleTopup();

        return view('affiliates.loans.top_up', compact('loans'));
    }
}
