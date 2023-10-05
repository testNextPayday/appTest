<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\LoanRequest;

class LoanRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:investor');    
    }
    
    public function index()
    {
        try{
            
             $loanRequests = LoanRequest::available()
                            ->with('repayment')
                            ->whereNull('investor_id')
                            ->latest()->paginate(12);
        }catch(\Execpection $e){
            return redirect()->response()->with("failure", $e->getMessage());
        }
        return view('investors.loan-requests.index', compact('loanRequests'));
    }
    
    public function assigned()
    {
        $investor = auth('investor')->user();
        
        $loanRequests = $investor->loanRequests()
                                ->available()
                                ->latest()
                                ->paginate(12);
        return view('investors.loan-requests.index', compact('loanRequests'));
    }
}
