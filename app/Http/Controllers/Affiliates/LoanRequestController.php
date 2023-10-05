<?php

namespace App\Http\Controllers\Affiliates;

use Paystack;
use Carbon\Carbon;

use App\Models\User;

use App\Models\Settings;
use App\Models\Affiliate;
use App\Models\LoanRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Traits\Managers\LoanRequestManager;

class LoanRequestController extends Controller
{
    use LoanRequestManager;
    
    public function __construct()
    {
        $this->middleware('auth:affiliate');
    }
    
    /**
     * Returns a list of affiliate created loan requests
     * 
     */
    public function index()
    {
        $affiliate = auth('affiliate')->user();
        
        $loanRequests = $affiliate->loanRequests()->paginate(30);
        
        return view('affiliates.loan-requests.index', compact('loanRequests'));
    }
    
    
    /**
     * Returns the affiliate loan creation page
     */
    public function create()
    {
     
        $loanApplicationFee = Settings::loanRequestFee();

        return view('affiliates.loan-requests.create', 
                    compact('loanApplicationFee'));
    }
    
    /**
     * Get Affiliate borrowers with request
     *
     * @param  mixed $request
     * @return void
     */
    public function getBorrowers(Request $request)
    {
        $name = $request->query('query');
        $affiliate = auth('affiliate')->user();
      
        $allVissible = $affiliate->settings('loan_vissibility') == 'view_all_loans';

        // COMMENT
        // If user can view all loans then we get all borrowers whose employers are mapped to this affiliate
        // Else we get only the referred borrowers whose employers are mapped to this affiliate
        $employerIDs = $affiliate->mappedEmployer()->pluck('employer_id')->toArray();

        $users =  $allVissible ? 
                            User::where('name', 'like', '%'.$name.'%')->with(['employments'])->whereHas('employments', function ($query) use ($employerIDs) {
                                return $query->whereIn('employer_id', $employerIDs);        
                            })->take(20)->get() : 
                            $affiliate->borrowers()->where('name', 'like', '%'.$name.'%')->with(['employments'])->whereHas('employments', function ($query) use ($employerIDs) {
                                return $query->whereIn('employer_id', $employerIDs);        
                            })->take(20)->get();

        return response()->json($users);
    }
    
    
    /**
     * Returns a loan request created by an affiliate
     */
    public function show(LoanRequest $loanRequest)
    {
        // TODO Ensure this lr actually belongs to affiliate
        
        return view('affiliates.loan-requests.show', 
                    compact('loanRequest'));
    }

     /**
     * resubmits a referred loan request and move it to pending
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models $loanRequest
     * @return void
     */
    public function resubmit(Request $request, LoanRequest $loanRequest)
    {
        try {
            
            $data = [
                'amount' => $request->newAmount,
                'success_fee' => $request->charge,
                'duration' => $request->duration,
                // 'expected_withdrawal_date' => Carbon::parse($request->expected_withdrawal_date)->toDateString(),
                'acceptance_code' => LoanRequest::generateCode(),
                'acceptance_expiry' => Carbon::now()->addHours(24)->toDateString(),
                'status'=> '0'
            ];
            
            if ($request->hasFile('bank_statement') && $request->file('bank_statement')->isValid())
                $data['bank_statement'] = $request->bank_statement->store('public/loan_requests/bank_statements');
            
            // if ($request->hasFile('pay_slip') && $request->file('pay_slip')->isValid())
            //     $data['pay_slip'] = $request->pay_slip->store('public/loan_requests/pay_slips');
            
            if($request->will_collect_incomplete == 'on') {
                $data['will_collect_incomplete'] = 1;
            }
            

            $loanRequest->update($data);

         

            $loanRequest->update(['emi' => $loanRequest->emi()]);

        }catch(\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Loan Request has been queued for approval');
    }
}
