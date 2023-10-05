<?php

namespace App\Http\Controllers\Employers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\LoanRequest;
use Carbon\Carbon;

use App\Notifications\Users\LoanRequestApprovalNotification;

class LoanRequestController extends Controller
{
    public function approve($code, $reference)
    {
        $loanRequest = LoanRequest::whereReference($reference)
            ->where('acceptance_code', $code)
            ->whereStatus(0)
            ->first();
        if(!$loanRequest) abort(404);
        if($loanRequest && now()->lt($loanRequest->acceptance_expiry) && $loanRequest->update(['status' => 1])) {
            //return a confirmation page
            $loanRequest->user->notify(new LoanRequestApprovalNotification($loanRequest));
            return view('employers.loan-requests.confirmation')->with(['status' => true, 'message' => 'Loan request approved']);
        }
        
        return view('employers.loan-requests.confirmation')
                ->with(['status' => false, 'message' => 'Acceptance window has expired']);
    }   
    
    public function getDeclineForm($code, $reference)
    {
        $loanRequest = $this->getLoanRequest($code, $reference);
        if(!$loanRequest) abort(404);
        
        return view('employers.loan-requests.decline', compact('code', 'reference'));
    }
    
    
    public function decline(Request $request)
    {
        $loanRequest = $this->getLoanRequest($request->code, $request->reference);
        if(!$loanRequest) abort(404);
     
        $this->validate($request, ['decline_reason' => 'required']);
        
        if($loanRequest && now()->lt($loanRequest->acceptance_expiry)) {
            $loanRequest->update(['status' => 5, 'decline_reason' => $request->decline_reason]);
            //return a confirmation page
            $loanRequest->user->notify(new LoanRequestApprovalNotification($loanRequest));
            return view('employers.loan-requests.confirmation')
                    ->with(['status' => false, 'message' => 'Loan request declined']);
        }
        //return a reasonable information page
        return view('employers.loan-requests.confirmation')
                ->with(['status' => false, 'message' => 'Declination window has expired']);
    }
    
    private function getLoanRequest($code, $reference)
    {
        $loanRequest = LoanRequest::whereReference($reference)
                                    ->whereAcceptanceCode($code)
                                    ->whereStatus(0)->first();
        
        return $loanRequest;
    }
}
