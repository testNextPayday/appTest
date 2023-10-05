<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Traits\LoanUtils;
use App\Models\Employment;
use App\Models\Employer;

use App\Models\LoanRequest;
use App\Remita\DAS\Customer;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Events\LoanRequestLiveEvent;

use App\Http\Controllers\Controller;
use App\Notifications\Users\LoanRequestLiveNotification;
use App\Notifications\Users\LoanRequestUpdatedNotification;
use App\Notifications\Users\LoanRequestApprovalNotification;


class LoanRequestController extends Controller
{
    use LoanUtils;
    
    public function index()
    {
        $loanRequests = LoanRequest::latest()->get();
        return view('admin.loan-requests.index', compact('loanRequests'));
    }
    
    public function create()
    {
        $users = User::with(['employments'])->get();
        return view('admin.loan-requests.new', compact('users'));
    }
    
    public function store(Request $request)
    {
        $validationRules = [
            'amount' => 'required',
            //'interest_percentage' => 'required',
            'duration' => 'required|integer|min:1',
            //'comment' => 'required',
            'expected_withdrawal_date' => 'required|date',
            'reference' => 'required|exists:users',
            'risk_rating' => 'required|min:1|max:5',
            'bank_statement' => 'required|file|mimes:jpeg,jpg,png,pdf|max:10240'
        ];
        
        $this->validate($request, $validationRules);
        
        $user = User::whereReference($request->reference)->first();
        if(!$user) return redirect()->back()->with('failure', 'User with that reference number does not exist')->withInput(); 
        
        
        $employment = $user->employments()->whereId($request->employment)->first();
        if (!$employment) return redirect()->back()->with('failure', 'Invalid Employment Selected')->withInput();
        
        
        $salary = $employment->net_pay;
        $maxAmount = ($salary * $request->duration) / 3;
        $employer = $employment->employer;
        
        $duration = (int) $request->duration;
        if ($duration > $employer->max_tenure) {
            return redirect()
                ->back()
                ->with('failure', 'This account cannot take a loan for more than ' . $employer->max_tenure . ' months')
                ->withInput();   
        }
        
        
        if ($maxAmount < $request->amount) {
            return redirect()
                ->back()
                ->with('failure', 'This account cannot take more '. number_format($maxAmount, 2). ' for this duration')
                ->withInput();   
        }
                
        if ($duration <= 3) {
            $interest_rate = $employer->rate_3;
        } else if ($duration > 3 && $duration <= 6) {
            $interest_rate = $employer->rate_6;
        } else {
            $interest_rate = $employer->rate_12;
        }
        
        $loanRequest = new LoanRequest();
        $reference = $loanRequest->generateReference();

        $data = [
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $request->amount,
            'interest_percentage' => $interest_rate,
            'comment' => $request->comment,
            'duration' => $request->duration,
            'expected_withdrawal_date' => Carbon::parse($request->expected_withdrawal_date)->toDateString(),
            'bank_statement' => $request->bank_statement->store('public/loan_requests/bank_statements'),
            'acceptance_code' => LoanRequest::generateCode(),
            'acceptance_expiry' => Carbon::now()->addHours(24),
            'status' => 2,
            'employment_id' => $employment->id
        ];
        
        if($request->will_collect_incomplete == 'on') {
            $data['will_collect_incomplete'] = true;
        }
        
        if ($loanRequest = $loanRequest::create($data)) { 
            
            //update loan request emi
            $loanRequest->update(['emi' => $loanRequest->emi()]);
            
            if (!$user->remita_auth_code) 
                $user->generateRemitaAuthCode();
            
            
            //send an Activation email to the employer
            //send an Email and SMS to the user
            //$user->notify(new LoanRequestLiveNotification($loanRequest));
            //$supervisor = new DynamicRecipient($employment->supervisor_email);
            //$supervisor->notify(new LoanRequestApprovalRequestNotification($loanRequest));
            return redirect()->back()->with('success', 'Loan request placed successfully.');
        }
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
  
    public function view($reference)
    {
        $loanRequest = LoanRequest::whereReference($reference)->first();
    
        if(!$loanRequest)
            return redirect()->back()->with('failure', 'Loan request does not exist');
        $user = $loanRequest->user;
        
        $loanRequests = $user->loanRequests()->where('id', '!=', $loanRequest->id)->latest()->get();
        
        return view('admin.loan-requests.show', compact('loanRequest', 'user', 'loanRequests'));
    }
    
    public function pending()
    {
        $loanRequests = LoanRequest::whereIn('status', [0,1,7])->with('user')->latest()->get();
        return view('admin.loan-requests.pending', compact('loanRequests'));
    }
    
    public function employerDeclined()
    {
        $loanRequests = LoanRequest::where('status', 5)->with('user')->latest()->get();
        $title = 'Employer Declined Requests';
        return view('admin.loan-requests.list', compact('loanRequests', 'title'));
    }
    
    public function pendingSetup()
    {
        // Get accepted loan requests and requests fully funded and still on status 2
        $loanRequests = LoanRequest::where('status', 4)
                            ->with('user', 'loan')
                            ->latest()
                            ->take(100)->get();
                            
        $loanRequests = $loanRequests->filter(function($request) {
            return $request->loan == null; 
        });
        
        return view('admin.loan-requests.pending-setup', compact('loanRequests'));
    }
    
    public function available()
    {
        $loanRequests = LoanRequest::whereStatus(2)->with('user')->latest()->get();
        return view('admin.loan-requests.available', compact('loanRequests'));
    }

    public function viewSalaryData(Customer $customer, $reference)
    {
        $loanRequest = LoanRequest::whereReference($reference)->first();
        $user = $loanRequest->user;
        
        $salaryData = $customer->getSalaryData($user);
        
        $gotValidData = $salaryData && $salaryData->status && strtolower($salaryData->status) === 'success' && $salaryData->responseCode === '00';
        
        if ($gotValidData && !$user->remita_id) {
            $user->update(['remita_id' => $salaryData->data->customerId]);
        }
        
        return view('admin.loan-requests.salary', compact('loanRequest', 'salaryData', 'gotValidData'));
    }
    
    public function approveLoanRequest(Request $request)
    {
        $validationRules = ['risk_rating' => 'required'];
        
        $this->validate($request, $validationRules);
        $data = [
            'status' => 4, 
            'risk_rating' => $request->risk_rating,
            // 'collection_plan' => $request->collection_plan
        ];
        
        try{
           
            DB::beginTransaction();
            $loanRequest = LoanRequest::findOrFail($request->request_id);

            $user = $loanRequest->user;

            if ($user->activeLoans()->count() > 0 && !$loanRequest->is_top_up && !$user->enable_salary_now_loan) {
                throw new \Exception('User has an active loan. Request cannot be approved. Topup or settle');
            }

            // if ($user->activeLoans()->count() > 0 && !$user->enable_salary_now_loan) {
            //     throw new \Exception('User has an active loan. Request cannot be approved. Topup or settle');
            // }


            if($loanRequest->update($data)) {
                //notify user
                
                //$loanRequest->user->notify(
                //    new LoanRequestLiveNotification($loanRequest)
                //);
                event(new LoanRequestLiveEvent($loanRequest));
               
            }
        }catch(\Exception $e){

            DB::rollback();
            
            return redirect()->back()->with('failure', $e->getMessage());
        }

        DB::commit();
        return redirect()->back()->with('success', 'Request has been approved');
    }
        
    /**
     * Declines a loan Request Admin Privileges
     *
     * @param  string $reference
     * @return void
     */
    public function declineLoanRequest(Request $request, $reference)
    {
        try {

            $loanRequest = LoanRequest::whereReference($reference)->first();

            if (!$loanRequest) {
                throw new \InvalidArgumentException('Loan Request not found');
            }

            $loanRequest->update(['status' => 6,'decline_reason'=>$request->decline_reason]);
            //return a confirmation page
            $loanRequest->user->notify(
                new LoanRequestApprovalNotification($loanRequest)
            );
            return redirect()->back()->with('success', 'Loan request has been declined');
           

        } catch (\Exception $e) {
           
            return redirect()->back()->with('failure', 'An error occurred. Please try again.');
        }
       
    }


     /**
     * Declines a loan Request Admin Privileges
     *
     * @param  string $reference
     * @return void
     */
    public function referLoanRequest(Request $request, $reference)
    {
        try {

            $loanRequest = LoanRequest::whereReference($reference)->first();

            if (!$loanRequest) {
                throw new \InvalidArgumentException('Loan Request not found');
            }

            $loanRequest->update(['status' => 7, 'decline_reason'=>$request->refer_reason]);
            //return a confirmation page
            $loanRequest->user->notify(
                new LoanRequestApprovalNotification($loanRequest)
            );
            return redirect()->back()->with('success', 'Loan request has been referred');
           

        } catch (\Exception $e) {
           
            return redirect()->back()->with('failure', 'An error occurred. Please try again.');
        }
       
    }
    
    /**
     * We edit and update our loan request
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function updateLoanRequest(Request $request)
    {

        // validating the request inputs
        $this->validate(
            $request,
            [
            'interest_percentage'=>'required',
            'amount'=>'required',
            'duration'=>'required'
            ]
        );
        // getting loan 
        try{

            $loanRequest = LoanRequest::whereReference($request->reference)->first();

            if (!$loanRequest) {

                throw new \InvalidArgumentException('Loan Request not found');
            }

            if ($loanRequest->status > 1) {
                throw new \DomainException('Loan Request cannot be edited because it has been approved');
            } 
            $loanRequest->amount = (int)$request->amount;
            $loanRequest->duration = $request->duration;
            $loanRequest->interest_percentage = $request->interest_percentage;

            $employment = Employment::find($loanRequest->employment_id);

            if (! $employment) {

                throw new \InvalidArgumentException('No employment found for loan request');
            }
            // get employer success_fee
            $employer = $employment->employer;

            $percentage = $employer->success_fee/100;
            $gx = 1-$percentage;
            $fx = $request->amount/$gx; // LA/1-SF
            $new_sf = round($fx - $request->amount, 2);

            $loanRequest->success_fee = $new_sf;
            $loanRequest->save();

            $loanRequest->refresh();

            $loanRequest->update(['emi'=>$loanRequest->emi()]);
            // update was successful
            $loanRequest->user->notify(
                new LoanRequestUpdatedNotification($loanRequest)
            );

        } catch (\Exception | Swift_TransportException $e){

            return redirect()->back()->with('failure', $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'Loan request has been successfully edited');

    }
    
    public function checkMaxRequestAmount($reference, $duration, $employment)
    {
        $user = User::whereReference($reference)->first();
        if(!$user) return response()->json(
                [
                    'status' => 0, 
                    'message' => 'Account with that reference number does not exist'
                ], 200);        
        $employment = $user->employments()->whereId($employment)->first();
        if (!$employment) return response()->json(
                [
                    'status' => 0, 
                    'message' => 'The selected employment does not match with selected user'
                ], 200);
        
        $salary = $employment->net_pay;
        $maxAmount = ($salary * $duration) / 3;
        return response()->json(['status' => 1 , 'max_amount' => number_format($maxAmount, 2), 'message' => 'Success'], 200);
    }
    
    public function checkMonthlyRepayment($duration, $employment, $amount)
    {
        $employment = Employment::find($employment);
        if (!$employment) return response()->json(
                [
                    'status' => 0, 
                    'message' => 'The selected employment does not match with selected user'
                ], 200);
        
        $employer = $employment->employer;
        
        $duration = (int) $duration;
        if ($duration <= 3) {
            $interestPercentage = $employer->rate_3;
        } else if ($duration > 3 && $duration <= 6) {
            $interestPercentage = $employer->rate_6;
        } else {
            $interestPercentage = $employer->rate_12;
        }
        
       // $rateOfInterest = ($interestPercentage/100) * $duration;
        
        //$emi = round($this->getEMI(($rateOfInterest)/$duration, $duration, -$amount), 2);
    
        $rate = $interestPercentage/100;
        $emi = $this->getFlatEmi($rate, $amount, $duration);
        
        return response()->json(['status' => 1 , 'emi' => number_format($emi, 2), 'message' => 'Success'], 200);
    }


    public function assignLoanRequest(Request $request, LoanRequest $loanRequest) {

        try {

           $loanRequest = LoanRequest::whereReference($request->reference)->first();

            $loanRequest->update(
                [
                    'placer_id'=>$request->affiliate_id,
                    'placer_type'=>'App\Models\Affiliate'
                ]
            );

        }catch (\Exception $e) {

            return $this->sendExceptionResponse($e);
        }

        return redirect()->back()->with('success', 'Loan Request Assigned Successfully');

    }


    public function unassignLoanRequest(Request $request, LoanRequest $loanRequest) {

        try {

            $loanRequest = LoanRequest::whereReference($request->reference)->first();
 
             $loanRequest->update(
                 [
                     'placer_id'=>null,
                     'placer_type'=>'' //placer type cannot be null
                 ]
             );
 
         }catch (\Exception $e) {
 
             return $this->sendExceptionResponse($e);
         }
 
         return redirect()->back()->with('success', 'Loan Request Unassigned Successfully');
    }
}