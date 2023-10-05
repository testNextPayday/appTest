<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\LoanRequest;
use App\Models\WalletTransaction;
use App\Models\Employment;
use App\Models\Settings;
use App\Models\User;

use Carbon\Carbon;
use Auth, DB, Paystack;

use App\Remita\DAS\LoanDisburser;
use App\Helpers\FinanceHandler;
use App\Models\Affiliate;
use App\Traits\Managers\LoanRequestManager;

use App\Notifications\Users\LoanRequestLiveNotification;
use App\Notifications\Users\LoanRequestRejectedNotification;
use App\Notifications\Users\LoanRequestPlacedNotification;

class LoanRequestController extends Controller
{
    use LoanRequestManager;
    
    public function index()
    {
        $loanRequests = auth('staff')->user()->loanRequests()->latest()->paginate(30);
        return view('staff.loan-requests.index', compact('loanRequests'));
    }

    public function getAffiliates()
    {
        return response()->json([
            'data' => Affiliate::latest()->get()
        ]);
    }
    
    /**
     * Returns a single loan request
     * 
     */
    public function view($reference)
    {
        $staff = auth('staff')->user();
        if($staff->manages('loan_request_setup') || $staff->manages('loan_request')){
            $loanRequest = LoanRequest::whereReference($reference)->first();
            if(! $loanRequest)return redirect()->back()->with('failure', 'Loan request not found');
            return view('staff.loan-requests.show', compact('loanRequest'));
        }
        $loanRequest = Auth::guard('staff')
                        ->user()
                        ->loanRequests()
                        ->whereReference($reference)
                        ->first();
        if (!$loanRequest) return redirect()->back()->with('failure', 'Loan request not found');
        
        return view('staff.loan-requests.show', compact('loanRequest'));
    }

    /**
     * Returns a paginated list of pendi loan requests
     * @return LoanRequest
     */
    public function pendingSetup()
    {
        $staff = auth()->guard('staff')->user();
        // Get accepted loan requests and requests fully funded and still on status 2
        $loanRequests = LoanRequest::where('status', 4)
                            ->with('user', 'loan')
                            ->latest()->take(100)
                            ->get();
                            
        $loanRequests = $loanRequests->filter(function($request) {
            return $request->loan == null; 
        });
        return view('staff.loan-requests.pending-setup', compact('loanRequests', 'staff'));
    }

    public function pending()
    {
        $loanRequests = LoanRequest::whereIn('status', [0,1,7])->with('user')->latest()->get();
        return view('staff.loan-requests.pending', compact('loanRequests'));
    }
    

    
    /**
     * Returns a paginated list of available loan requests
     * @return LoanRequest
     */
    public function active()
    {
        $staff = auth()->guard('staff')->user();
        $loanRequests = LoanRequest::whereStatus(2)
                // ->where('staff_id', '!=', Auth::id())
                ->with('user')
                ->where('percentage_left', '>', 0)
                ->whereNull('investor_id')
                ->latest()->paginate(30);
        return view('staff.loan-requests.active', compact('loanRequests', 'staff'));
    }
    
    /**
     * Returns staff loan creation page
     */
    public function create()
    {   
        $loanApplicationFee = Settings::loanRequestFee();

        return view('staff.loan-requests.new', compact('loanApplicationFee'));
    }

    public function getBorrowers(Request $request)
    {
        $query = $request->query('query');
        $users = User::where('name', 'like', '%'.$query.'%')->with(['employments'])->take(20)->get();
        return response($users);
    }
    
    
    
    public function checkLoanRequestMandateStatus(LoanRequest $loanRequest, FinanceHandler $financeHandler)
    {
        $response = $this->checkMandateStatus($loanRequest, $financeHandler);
        
        if ($response['status'] === 'success' && $response['action'] === 1) {
            // redirect to loan page
            return redirect()->route('staff.loans.view', ['reference' => $response['reference']]);
        }
        
        // return redirect back
        return redirect()->back()->with($response['status'], $response['message']);
    }
}
