<?php

namespace App\Http\Controllers\Users;


use Illuminate\Support\Facades\DB;


use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Employer;
use App\Models\Investor;
use App\Models\Settings;
use App\Models\Affiliate;

use App\Models\Employment;
use App\Models\LoanRequest;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Traits\EncryptDecrypt;

use App\Helpers\FinanceHandler;
use App\Models\UpfrontInterest;

use App\Models\IncompleteRequest;

use App\Models\WalletTransaction;
use App\Paystack\PaystackService;
use App\Repositories\SmsRepository;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Recipients\DynamicRecipient;
use Illuminate\Support\Facades\Auth;
use App\Unicredit\Collection\Utilities;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\RefundTransactionService;
use App\Events\PaystackCustomerRefundEvent;
use App\Services\TransactionVerificationService;
use App\Services\LoanRequest\UserLoanRequestService;
use App\Services\UpfrontInterest\UpfrontInterestService;
use App\Notifications\Users\LoanRequestPlacedNotification;
use App\Notifications\Users\LoanRequestCancelledNotification;
use App\Notifications\Users\LoanRequestApprovalRequestNotification;
use App\Services\Lydia\LydiaService;
use Exception;
use Unicodeveloper\Paystack\Paystack;


class LoanRequestController extends Controller
{
    use EncryptDecrypt;

    protected $imageService;
    protected $refundService;
    protected $lydiaService;
    
    public function __construct(ImageService $service, RefundTransactionService $refundService) 
    {
        $this->imageService = $service;
        $this->refundService = $refundService;
        // $this->lydiaService = $lydiaService;
    }

    public function index()
    {
        $loanRequests = Auth::user()->loanRequests()
                                    ->latest()
                                    ->get();
        return view('users.loan-requests.index', compact('loanRequests'));
    }
    
    public function create()
    {
        $user = Auth::guard('web')->user();
        $employment = $user->employments()->with('employer')->get();        
        
        if(!$user->profileIsComplete()) {
            return redirect()->route('users.profile.index')->with('failure', 'Please complete your profile first');
        }

        if ($user->activeLoans()->count() > 0 && !$user->enable_salary_now_loan) {
            return redirect()->back()->with('failure', 'You have an active loan running. Kindly Settle or Topup');
        }
        if($user->enable_salary_now_loan && $user->activeLoans()->count() > 0 && $employment->count() < 2){
            return redirect()->back()->with('failure', 'You Already Have an Active Loan, Add a Second Employer to Book Loan');
        }
        if($user->enable_salary_now_loan && $user->activeLoans()->count() > 1){
            return redirect()->back()->with('failure', 'You have active loans running. Kindly Settle or Topup');
        }

        if(!$user->employerIsNotPrimary()) {
            return redirect()->back()->with('failure', 'You can only book a loan using our primary employers. Create a new employer and delete non primary');
        }

        // if (!$user->bvnVerified()) {
        //     return redirect()->route('users.profile.index')->with('failure', 'Please verify your bvn before ');
        // }
        $users = User::get(['name','id', 'reference']);
        
        return view('users.loan-requests.new', ['users'=> $users]);
    }


    protected function verifyPayment($request, $verifyService)
    {
      
        $user = Auth::guard('web')->user();
        $trnxRef = $request->reference;
        // Verify payment
        $paymentVerification = UserLoanRequestService::verifyApplicationFee($trnxRef, $verifyService);

        if (! $paymentVerification['status']) {
            throw new \Exception($paymentVerification['message']);
        }

        // save billing information
        $authorization = $paymentVerification['data']['authorization'];           
        $billingData = UserLoanRequestService::extractBillingInfo($authorization);
        // Verify Billing
        if(UserLoanRequestService::cardCanBookLoan($billingData, $request->duration)){
            UserLoanRequestService::createBillingRecord($billingData, $user);
            return ['status'=>true, 'message'=>'Verification Successful', 'reference'=>$trnxRef];
        }

        return ['status'=>false, 'message'=>'Card Cannot Book Loan', 'reference'=>$trnxRef];
    }

    public function store(Request $request, TransactionVerificationService $verifyService){

        try{
           
            DB::beginTransaction();   
            
            // This ensures that the verifyResponse variable is in scope when a validation error occurs
            $verifyResponse = $this->verifyPayment($request, $verifyService);

            if (!$verifyResponse['status']) {
                throw new \Exception($verifyResponse['message']);
            }

            $this->validate($request, $this->getRules());
            // generate user loan request data
            $loanRequest = UserLoanRequestService::createLoanRequest($request);

            // Create Lidya Mandate
            // if ($loanRequest->employment->employer->collection_plan == 103) {
            //     $user = Auth::user();
            //     $this->lydiaService->createMandate([
            //         'amount' => $loanRequest->amount,
            //         'frequency' => 'monthly',
            //         'start_date' => Carbon::now()->addDay()->format('Y-m-d'),
            //         'duration' => $loanRequest->loan_period == 'weekly' ? 7 : 30,
            //         'bvn' => $user->bvn,
            //         'name' => $user->name,
            //         'phone_number' => $user->phone,
            //         'email' => $user->email
            //     ]);
            // }

            UserLoanRequestService::handleLoanRequestFee($request->application_fee, $loanRequest);
            //Log::info(json_encode($verifyResponse));            
            
            if($loanRequest->upfront_interest){               
                (new UpfrontInterestService())->create($loanRequest, $request);
            }

            // once it succeeds clear out old incomplete requests
            IncompleteRequest::clearOutIncompleteRequests();
            
            DB::commit();            
            //UserLoanRequestService::sendNotifications($loanRequest);

            // if ($loanRequest->employment->employer->collection_plan == 103) {
            //     return response()->json(['status'=>true, 'message'=> 'You loan request was successfully sent and awaiting approval, please check your mail to complete Lydia setup.'], 200);
            // }else {
            //     // return response()->json('Success');
            // }

            return response()->json(['status'=>true, 'message'=> 'You loan request was successfully sent and awaiting approval, please check notification page for next step.'], 200);

        }catch(\Exception $e){
            DB::rollback();
            
            // This ensures that the verifyResponse variable is in scope when a validation error occurs
            $verifyResponse = $this->verifyPayment($request, $verifyService);

            IncompleteRequest::serializeLoanRequest($request, $e->getMessage(), $verifyResponse);

            if ($e instanceof \DomainException){
                return response()->json($e->getMessage(), 422);
            }
            // Log::channel('unexpectederrors')->debug(
            //     $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine()
            // );

            return response()->json($e->getMessage(), 422);
        }

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
    
    public function view(LoanRequest $loanRequest)
    {   
        if($loanRequest->user_id != Auth::id())
            return redirect()->back()->with('failure', 'Loan request does not exist');
        return view('users.loan-requests.view', compact('loanRequest'));
    }
    
    public function cancel(LoanRequest $loanRequest, FinanceHandler $financeHandler)
    {
        $borrower = auth()->guard('web')->user();
        $data = ['status' => 3];
        if($loanRequest->update($data)) {
            $borrower->notify(new LoanRequestCancelledNotification($loanRequest));
            $activeFunds = $loanRequest->funds;
            foreach ($activeFunds as $fund) {
                $fund->update(['status' => 3]);
                $investor = $fund->investor;
                //update bidders wallet balance
                
                $code = config('unicredit.flow')['loan_fund_rvsl'];
                $financeHandler->handleDouble(
                    $borrower, $investor, $fund->amount, $loanRequest, 'ETW', $code
                );
                
                $investor->notify(new LoanRequestCancelledNotification($loanRequest));
            }
            return redirect()->back()->with('success', 'Request cancelled successfully');
        }
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function delete(LoanRequest $loanRequest)
    {
        if($loanRequest->delete()) {
            return redirect()->back()->with('success', 'Request deleted successfully');
        }
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function accept(LoanRequest $loanRequest)
    {
        $user = auth()->user();
        
        if(!$loanRequest->exists || $loanRequest->user_id != $user->id)
            return redirect()->back()->with('failure', 'Loan Request Not Found');
            
        // NOTE: Here money is in borrowers escrow
        // Update the loan request as accepted without consulting a gateway.
        // Admin will set up collection on disbursement

        if ($loanRequest->update(['status' => 4])) {
            return redirect()->back()->with('success', 'Funds accepted. Admin will now set up a loan for you');
        }
        
        return redirect()->back()->with('failure', 'Funds cannot be accepted at this moment. Please try again later');
    }
    
    public function assignToInvestor(Request $request, LoanRequest $loanRequest)
    {
        if ($loanRequest->exists && $loanRequest->user_id == auth()->id()) {
            
            $investor = Investor::whereReference($request->reference)->first();
            
            if (!$investor)
                return redirect()->back()->with('failure', 'Investor not found')->withInput();
                
            
            $loanRequest->update(['investor_id' => $investor->id]);
            
            return redirect()->back()->with('success', 'Loan Request assigned successfully');
            
        }
        
        return redirect()->back()->with('failure', 'Loan Request not found');
    }
    
    private function preparePayment(Request $request, User $user, $data, $employer)
    {
        $loanRequestFee = $employer->application_fee < 1 ? Settings::loanRequestFee() : $employer->application_fee;

        $charge = ($loanRequestFee + paystack_charge($loanRequestFee)) * 100;
       
        $request->request->add(['reference' => Paystack::genTranxRef()]);
        $request->request->add(['key' => config('paystack.secretKey')]);
        $request->request->add(['amount' => $charge]);
        $request->request->add(['tokenize' => true]);
        $request->request->add(['email' => $user->email]);
        $request->request->add(['metadata' => $data]);
        $request->request->add(['callback_url' => route('users.loan-requests.store.pay')]);
    }
    
    private function getRules()
    {
        return [
            'amount' => 'required',
            'interest_percentage' => 'required',
            'duration' => 'required|integer|min:1',
            //'comment' => 'required',
            // 'expected_withdrawal_date' => 'required|date',
            'employment_id' => 'required',
            'loan_period' => 'string'
        ];   
    }
    
    private function checkRequirements()
    {
        $check = [
            'status' => true,
            'message' => 'All Ok'
        ];
        
        //check amount and time against salary
        $employment = Employment::find(request('employment_id'));
        if(!$employment) {
            $check['status'] = false;
            $check['message'] = 'Employment not found';
            return $check;
        }
        
        
        $salary = $employment->net_pay;
        
        if (($salary * request('duration')) / 3 < request('amount')) {
            $check['status'] = false;
            $check['message'] = 'You cannot take more than a third of your total salary for this duration';
            return $check;
        }
            
        
        $employer = $employment->employer;
        $duration = (int) request('duration');
        
        if ($duration > $employer->max_tenure) {
            $check['status'] = false;
            $check['message'] = 'You cannot take a loan for more than ' . $employer->max_tenure . ' months with this employer';
            return $check;
        }    
        
        return $check;
    }



   

}