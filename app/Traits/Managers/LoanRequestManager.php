<?php

namespace App\Traits\Managers;

use App\Models\Affiliate;
use App\Models\Staff;
use App\Models\Employment;
use App\Models\LoanRequest;
use App\Models\Settings;
use App\Models\Employer;
use App\Models\User;

use App\Http\Requests\LoanRequests\StoreRequest;
use App\Notifications\Users\LoanRequestPlacedNotification;
use App\Helpers\FinanceHandler;

use Carbon\Carbon;
use Paystack, DB, Log;
use App\Traits\LoanUtils;


trait LoanRequestManager
{
    use LoanUtils;
    
    
    public function checkMaxRequestAmount($reference, $duration, $employment)
    {
        $personnel = auth()->user();
       
        
        // if ($personnel instanceof Affiliate)
        //     $query = $personnel->borrowers();

        // else if ($personnel instanceof Staff)
        //     $query = $personnel->users();
            
        // else 
            
        //     return response()->json(['message' => 'Unauthorized'], 401);
        $query  = new User();
        
            
        
        $user = $query->whereReference($reference)->first();
        
        
        
        if(!$user)
            return response()->json(['message' => 'Account with that reference number does not exist '], 404); 
        
                
        $employment = $user->employments()->whereId($employment)->first();
        
        if (!$employment)
            return response()->json([
                'message' => 'The selected employment does not match with selected user'
            ], 404);
        $employer = $employment->employer;
        $salary = $employment->net_pay;
        $maxAmount = ($salary * $duration) / 3;

        // if employer has salary upgrade enables
        if ($employer->upgrade_enabled == 1) {
            $maxAmount = ($user->salary_percentage/ 100) * $maxAmount;
        }
        return response()->json([
            'max_amount' => $maxAmount, 
            'loanLimit' => $employer->loan_limit, 
            'success_fee' => $employer->success_fee,
            'message' => 'Success'], 200);
    }
    
    
    public function checkMonthlyRepayment($duration, $employment, $amount)
    {
        $employment = Employment::find($employment);
        if (!$employment) return response()->json(
                [
                    'message' => 'The selected employment does not match with selected user'
                ], 404);
        
        $employer = $employment->employer;
        
        $duration = (int) $duration;
        if ($duration <= 3) {
            $interestPercentage = $employer->rate_3;
            $fee = $employer->fees_3;
        } else if ($duration > 3 && $duration <= 6) {
            $interestPercentage = $employer->rate_6;
            $fee = $employer->fees_6;
        } else {
            $interestPercentage = $employer->rate_12;
            $fee = $employer->fees_12;
        }
        
      
        $mgt_fee  = (($fee / 100) * $amount);
      
        $emi = $this->pmt($amount, $interestPercentage,$duration) + $mgt_fee;
     
        //$rateOfInterest = ($interestPercentage/100) * $duration;
        //$emi = round($this->getEMI(($rateOfInterest)/$duration, $duration, -$amount), 2);
    
        return response()->json(['emi' => number_format($emi, 2), 'message' => 'Success'], 200);
    }

    
    /**
     * Creates a new loan request for an authorized personnel
     * 
     */
    public function store(StoreRequest $request)
    {
        try {

        
            $enable_request = Settings::where('slug', 'enable_loan_request')->first()->value;
            if($enable_request == 0) return redirect()->back()->with('failure', 'Loan Request cannot be made at this point in time');
        
            $personnel = auth()->user();
            
            if (!$userQuery = $this->resolveUserQuery($personnel)) {
                return redirect()->back()->with('failure', 'Unauthorized Action');
            }
            
            $user = $userQuery->whereReference($request->reference)->first();

            if ($user->activeLoans()->count() > 0 && !$request->is_top_up) {

                return redirect()->back()->with('failure', 'User already has an active loan running');
            }
            
            if(!$user) return redirect()->back()
                    ->with('failure', 'Account with that reference number does not exist or is not yours')
                    ->withInput(); 
                    
                    
            $employment = $user->employments()->whereId($request->employment)->first();
            $employer = optional($employment)->employer;

            $requestValidation = $this->validateRequest($request, $employment, $employer);
            
            if (!$requestValidation['status'])
                return redirect()->back()->with('failure', $requestValidation['message'])->withInput();
            
            
            //$loanRequest = new LoanRequest();
            //$reference = $loanRequest->generateReference();

            $data = $this->generateLoanRequestData($personnel, $user, $employer, $request);

            $this->preparePayment($request, $user, $data, $employer);

            if(auth('affiliate')->check() || auth()->guard('staff')){
    
                if($request->amount > $employer->loan_limit && $employer->loan_limit != 0){
                    throw new \Exception('Exceeded loan limit');
                }
                $loanRequest = LoanRequest::create($data);

                if ($loanRequest->disbursalAmount() < 0) {
                    $loanRequest->delete();
                    throw new \Exception('This loan will lead to a negative disbursal amount');
                }
            
                return redirect()->back()->with('success', 'Loan Application successful');
              
            }
            
            
        } catch(\Exception $e) {
            return redirect()->back()->with('failure', $e->getMessage());
        }
      
       
        return Paystack::getAuthorizationUrl()->redirectNow();
    }
    
    
    /**
     *  Cancels a loan request initiated by a personnel 
     */
    public function cancel(LoanRequest $loanRequest, FinanceHandler $financeHandler)
    {
        $personnel = auth()->user();
        
        
        if (!$loanRequest->wasPlacedBy($personnel)) {
            return redirect()->back()->with('failure', 'Unauthorized action');
        }
        
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
            
            DB::commit();
            return redirect()->back()->with('success', 'Loan Request Cancelled Successfully');
        
        } catch(Exception $e) {
            
            DB::rollback();
            
            return redirect()->back()->with('failure', 'An error occurred. Please try again');
        }
        
    }
    
    
    /**
     * accepts funds from a loan request
     * 
     */
    public function accept(LoanRequest $loanRequest)
    {
        $personnel = auth()->user();
        
        Log::info(!$loanRequest->exists);
        Log::info($loanRequest);
        Log::info(!$loanRequest->wasPlacedBy($personnel));
        Log::info($personnel);
        if(!$loanRequest->exists || !$loanRequest->wasPlacedBy($personnel))
            return redirect()->back()->with('failure', 'Unauthorized Action');
            
        // NOTE: Here money is in borrowers escrow
        // Update the loan request as accepted without consulting a gateway.
        // Admin will set up collection on disbursement

        if ($loanRequest->update(['status' => 4])) {
            return redirect()->back()->with('success', 'Funds accepted. Admin will now set up a loan for this customer');
        }
        
        return redirect()->back()->with('failure', 'Funds cannot be accepted at this moment. Please try again later');
    }
    
    /**
     * Handles paystack response for a loan request application
     * 
     */
    public function handleApplicationPaymentResponse(FinanceHandler $financeHandler)
    {
        
        $data = Paystack::getPaymentData();
        
        if (!$data['status']) {
            // got back to application page
            return redirect()->route($this->getFailedPaymentRedirect())
                            ->with('failure', 'Error paying application fees');
        }
        
        try {
            
            DB::beginTransaction();
            
            // create loan request
            $loanData = $data['data']['metadata'];
            $loanRequest = LoanRequest::create($loanData);
            
            // update loan request emi
            //$loanRequest->update(['emi' => $loanRequest->emi()]);
            
            
            $user = $loanRequest->user;
            
            // save billing information
            $authorization = $data['data']['authorization'];
            $user->billingCards()->create($authorization);
            
            
            // Generate remita code if necessary
            if (!$user->remita_auth_code) 
                $user->generateRemitaAuthCode();
                
            // Create payments
            $this->createLoanRequestPayments($financeHandler, $user, $loanRequest);
            
            DB::commit();
            
            $user->notify((new LoanRequestPlacedNotification($loanRequest))->delay(30));
            
            return redirect($this->loanRequestViewRoute($loanRequest))
                        ->with('success', 'Loan request placed successfully.');
            
        } catch(Exception $e) {
            
            Log::channel('paystack')->info($e);
            
            DB::rollback();
            
            return redirect()->back()->with('failure', 'An error occurred. Please try again later or contact Admin');
        }
        
    }
    
    
    
    /**
     * Attaches necessary requirements to paystack payload
     * 
     */
    private function preparePayment(StoreRequest $request, User $user, $data, $employer)
    {
        $default_charge = Settings::loanRequestFee();

        $charge = $employer->application_fee < 1 ? $default_charge : $employer->application_fee;

        $paystackCharge = (0.015 * $charge) + $charge > 2000 ? 100 : 0;
        request()->request->add(['reference' => Paystack::genTranxRef()]);
        request()->request->add(['key' => config('paystack.secretKey')]);
        request()->request->add(['amount' => ($charge * 100) + ($paystackCharge * 100)]);
        request()->request->add(['tokenize' => true]);
        request()->request->add(['email' => $user->email]);
        request()->request->add(['metadata' => $data]);
        
        if (auth('affiliate')->check())
            request()->request->add(['callback_url' => route('affiliates.loan-requests.store.pay')]);
        else if (auth('staff')->check())
            request()->request->add(['callback_url' => route('staff.loan-requests.store.pay')]);
        
            
    }
    
    
    /**
     * Builds the user query for the specified personnel
     * 
     * @param [App\Models\Affiliate | App\Models\Staff] $personnel
     * 
     * @return QueryBuilder | boolean
     */
    private function resolveUserQuery($personnel)
    {
        // if ($personnel instanceof Affiliate)
        //     return $personnel->borrowers();
        
        // else if ($personnel instanceof Staff)
        //     return $personnel->users();
            
        // else 
        //     return false;
        return new User();
    }
    
    
    public function generateLoanRequestData($personnel, User $user, Employer $employer, $request)
    {

        $placer_id = (isset($request->affiliate_id)) ? $request->affiliate_id : $personnel ;
        $placer_type = (isset($request->affiliate_id)) ? 'App\Models\Affiliate' : get_class($personnel) ;
        

        $affiliate_type = $employer->affiliate_payment_method;
        $data = [
            'user_id' => $user->id,
            'amount' => $request->newAmount,
            'success_fee' => $request->charge,
            'interest_percentage' => $this->resolveInterestRate($request->duration, $employer),
            'comment' => $request->comment,
            'duration' => $request->duration,
            // 'expected_withdrawal_date' => Carbon::parse($request->expected_withdrawal_date)->toDateString(),
            'expected_withdrawal_date' => Carbon::now()->toDateString(),
            'acceptance_code' => LoanRequest::generateCode(),
            'acceptance_expiry' => Carbon::now()->addHours(24)->toDateString(),
            //'bank_statement' => $request->bank_statement ? $request->bank_statement->store('public/loan_requests/bank_statements') : null,
            'status' => 1,
            'placer_type' => $placer_type,
            'placer_id' => $placer_id,
            'employment_id' => $request->employment,
            'is_top_up' => $request->is_top_up == "true" ? true : false,
            'loan_referenced' => $request->loan_referenced,
            'affiliate_repayment_type' => $affiliate_type

        ];

        if ($request->bankstatementcleared && ! $request->hasFile('bank_statement')) {

            $statementRequest  = $user->lastSuccessBankStatementRequest();

            $data['bank_statement'] = optional($statementRequest)->request_doc ?? null;
        }

        if ($request->hasFile('bank_statement') && $request->file('bank_statement')->isValid()) {

            $data['bank_statement'] = $request->bank_statement ? $request->bank_statement->store('public/loan_requests/bank_statements') : null;
        }
        
        if($request->will_collect_incomplete == 'on') {
            $data['will_collect_incomplete'] = 1;
        }
        
        return $data;
    }
    
    
    private function resolveInterestRate($duration, Employer $employer)
    {
        if ($duration <= 3) {
            return $employer->rate_3;
        } else if ($duration > 3 && $duration <= 6) {
            return $employer->rate_6;
        } else {
            return $employer->rate_12;
        }
    }
    
    
    private function validateRequest(StoreRequest $request, Employment $employment = null, Employer $employer = null)
    {
        $response['status'] = false;
        
        if (!$employment) {
            $response['message'] = 'Invalid Employment';
            return $response;
        }
        
        if (!$employer) {
            $response['message'] = 'Employment not found';
            return $response;
        }
        
        if ((int)$request->duration > $employer->max_tenure) {
            $response['message'] = "This account cannot take a loan for more than $employer->max_tenure months";
            return $response;   
        }
        
        
        $maxAmount = ($employment->net_pay * $request->duration) / 3;
        
        if ($maxAmount < $request->amount) {
            $response['message'] = 'This account cannot take more '. number_format($maxAmount, 2). ' for this duration';
            return $response;   
        }
           
           
        $response['status'] = true;
        return $response;
    }
    
    
    private function getFailedPaymentRedirect()
    {
        // Resolve routes
        if (auth('affiliate')->check()) {
            return 'affiliates.loan-requests.create';
        } else {
            return 'staff.loan-requests.create';
        }
    }
    
    
    /**
     * Returns the route required to view a loan request
     * 
     * @param App\Models\LoanRequest $loanRequest
     * 
     * @return route
     */
    private function loanRequestViewRoute(LoanRequest $loanRequest)
    {
        // Resolve routes
        if (auth('affiliate')->check()) {
            return route('affiliates.loan-requests.show', ['loanRequest' => $loanRequest->reference]);
        } else {
            return route('staff.loan-requests.view', ['reference' => $loanRequest->reference]);
        }
    }
    
    
    /**
     * Creates payments for loans requests
     * 
     * @param FinanceHandler $financeHandler
     * @param User $user
     * @param LoanRequest $loanRequest
     * 
     * @return bool
     */
    private function createLoanRequestPayments(FinanceHandler $financeHandler, User $user, LoanRequest $loanRequest)
    {
        $code = config('unicredit.flow')['loan_request'];
        
        $applicationFee = Settings::loanRequestFee();
        
        $financeHandler->handleSingle(
            $user, 'credit', $applicationFee, $loanRequest, 'W', $code
        );
        
        $financeHandler->handleSingle(
            $user, 'debit', $applicationFee, $loanRequest, 'W', $code
        );
        
        return true;
    }
}