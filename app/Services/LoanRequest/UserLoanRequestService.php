<?php
namespace App\Services\LoanRequest;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Settings;
use App\Models\LoanRequest;
use App\Helpers\FinanceHandler;
use App\Paystack\PaystackService;
use App\Helpers\TransactionLogger;
use App\Recipients\DynamicRecipient;
use Illuminate\Support\Facades\Auth;
use App\Unicredit\Collection\Utilities;
use App\Services\TransactionVerificationService;
use App\Notifications\Users\LoanRequestPlacedNotification;
use App\Notifications\Users\LoanRequestApprovalRequestNotification;
use App\Services\Lydia\LydiaService;

class UserLoanRequestService
{
    
    /**
     * Verify Application Payment
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public static function verifyApplicationFee($ref, $service)
    {       
        $gatewayResponse = $service->verifyTransaction($ref);
        $verificationResponse = $service->verificationSuccessful();
        $gatewayMsg = $gatewayResponse['data']['gateway_response'];
        $response = ['status'=>$verificationResponse, 'message'=> $gatewayMsg, 'data'=> $gatewayResponse['data']];
        return $response;
    }
    
    
    /**
     * Record Application Fee
     *
     * @param  mixed $applicationFee
     * @return void
     */
    public static function handleLoanRequestFee($applicationFee, $loanRequest)
    {
        $user = Auth::guard('web')->user();

        $financeHandler = new FinanceHandler(new TransactionLogger);

        $code = config('unicredit.flow')['loan_request'];
        
        $applicationFee = Settings::loanRequestFee();

        $financeHandler->handleSingle(
            $user, 'credit', $applicationFee, $loanRequest, 'W', $code
        );

        $financeHandler->handleSingle(
            $user, 'debit', $applicationFee, $loanRequest, 'W', $code
        );
        
    }
    
    /**
     * Store loan Request
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Models\LoanRequest $loanRequest
     */
    public static function createLoanRequest($request)
    {
        $data = self::generateLoanRequestData($request);

        $referrer = $request->affiliate_code;

        $placer = Utilities::resolveAffiliateFromCode($referrer);

        unset($data['affiliate_code']);
        $loanRequest = LoanRequest::create($data);        
        //update loan request emi
        
        $loanRequest->update(['emi' => $loanRequest->emi()]);
        if ($placer) {
            $loanRequest->update(
                [
                    'placer_id'=>$placer->id, 
                    'placer_type'=> get_class($placer)
                ]
            );
        }
        $loanRequest->refresh();
        return $loanRequest;
    }

    
    /**
     * Create a billing Data
     *
     * @param  mixed $billingData
     * @param  mixed $user
     * @return void
     */
    public static function createBillingRecord($billingData, $user) 
    {
        $user->billingCards()->create($billingData);
            
            if (!$user->remita_auth_code) {
                
                $user->generateRemitaAuthCode();
            }
    }
    
    /**
     * Send Loan Request Notifications
     *
     * @param  \App\Models\LoanRequest $loanRequest
     * @return void
     */
    public static function sendNotifications($loanRequest) 
    {
        $user = $loanRequest->user;

        $user->notify(new LoanRequestPlacedNotification($loanRequest));
            
        $employment = $loanRequest->employment;
        if (app()->environment() == 'production') {
            $supervisor = new DynamicRecipient($employment->employer->approver_email);
            $supervisor->notify(new LoanRequestApprovalRequestNotification($loanRequest)); 
        }
        
    }
        
    /**
     * Generate loan Request data
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public static function generateLoanRequestData($request) {
        //$request->employment_id
        $employment = Auth::user()->employments()->with('employer')->get()->last();
        $affiliate_type = $employment->employer->affiliate_payment_method;
        $upfront_interest = $employment->employer->upfront_interest;
        $data =  [
            'user_id' => Auth::id(),
            'affiliate_code'=>$request->affiliate_code,
            'amount' => $request->amount,
            'success_fee' => $request->charge,
            'interest_percentage' => $request->interest_percentage,
            'comment' => $request->comment,
            'duration' => $request->duration,
            // 'expected_withdrawal_date' => Carbon::parse($request->expected_withdrawal_date)->toDateString(),
            'expected_withdrawal_date' => Carbon::now()->toDateString(),
            'acceptance_code' => LoanRequest::generateCode(),
            'acceptance_expiry' => Carbon::now()->addHours(24)->toDateString(),
            'employment_id' => $request->employment_id,
            'loan_referenced'=>$request->loan_referenced,
            'is_top_up'=> $request->is_top_up == "true" ? 1 : 0,
            'placer_type'=> '0', // default value
            'upfront_interest' => $upfront_interest,
            'affiliate_repayment_type' => $affiliate_type,
            'loan_period' => $request->loan_period,
            "guarantor_first_name" => $request->guarantor_first_name,
            "guarantor_last_name" => $request->guarantor_last_name,
            "guarantor_phone" => $request->guarantor_phone,
            "guarantor_email" => $request->guarantor_email,
            "guarantor_bvn" => $request->guarantor_bvn,
            "guarantor_first_name_2" => $request->guarantor_first_name_2,
            "guarantor_last_name_2" => $request->guarantor_last_name_2,
            "guarantor_phone_2" => $request->guarantor_phone_2,
            "guarantor_email_2" => $request->guarantor_email_2,
            "guarantor_bvn_2" => $request->guarantor_bvn_2,
            "is_setoff" => $request->is_setoff,
        ];

        // When Mono is enabled
        if ($request->enableMono) {
            $data['bank_statement'] = $request->bank_statement;
        }

        // This will work for my bank statement
        if ($request->bankstatementcleared && ! $request->enableMono) {

            $user = Auth::user();

            $statementRequest  = $user->lastSuccessBankStatementRequest();

            $data['bank_statement'] = optional($statementRequest)->request_doc ?? null;
        }

        // if ($request->hasFile('bank_statement') && $request->file('bank_statement')->isValid()) {

        //     $url = 'public/loan_requests/bank_statements/';

        //     //$data['bank_statement'] = $imageService->compressImage($request->bank_statement, $url);
        //     $data['bank_statement'] = $request->bank_statement->store('public/loan_requests/bank_statements');
        // }

        if($request->will_collect_incomplete == 'on') {
            $data['will_collect_incomplete'] = 1;
        }

        return $data;
    }


    
    /**
     * Extract Billing information
     *
     * @param  mixed $authorization
     * @return void
     */
    public static function extractBillingInfo($authorization)
    {
        return [
            'authorization_code'=>$authorization['authorization_code'],
            'bin'=>$authorization['bin'],
            'last4'=>$authorization['last4'],
            'exp_month'=>$authorization['exp_month'],
            'exp_year'=>$authorization['exp_year'],
            'card_type'=>$authorization['card_type'],
            'bank'=>$authorization['bank'],
            'country_code'=>$authorization['country_code'],
            'brand'=>$authorization['brand'],
            'reusable'=>$authorization['reusable'],
            'signature'=>$authorization['signature'],
        ];
    }

    public static function cardCanBookLoan($cardInfo, $duration)
    {

        if($cardInfo['reusable'] != true){

            throw new \Exception("Your card authorization must be reusable to complete setup");
        }

        $cardExpiryDate = $cardInfo['exp_year']." ".$cardInfo['exp_month'];

        $loanCreatedDate = Carbon::now()->addMonths($duration);

        $loanExpiryDate = $loanCreatedDate->format("Y m");

        if($cardExpiryDate <= $loanExpiryDate){

            throw new \Exception("This card will expire before the loan");
        }

        return true;
    }
}