<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\LoanFund;
use App\Models\WalletTransaction;
use App\Models\Loan;
use App\Models\User;
use App\Models\RepaymentPlan;
use App\Models\Settings;
use App\Models\Fee;

use App\Traits\EncryptDecrypt;
use App\Traits\Utilities;
use App\Traits\LoanUtils;

use App\Notifications\Users\LoanAcceptedNotification;

// use App\Helpers\LoanRepayment as Repayment;
use App\Helpers\FinanceHandler;
use App\Remita\DAS\LoanDisburser;

use Carbon\Carbon;
use Auth, Validator, DB, Session;

class LoanFundController extends Controller
{
    use EncryptDecrypt, LoanUtils;
    
    public function rejectLoan(LoanRequest $loanRequest, FinanceHandler $financeHandler)
    {
        $user = Auth::user();
        $loanFunds = $loanRequest->funds;
        DB::beginTransaction();
        try {
            foreach($loanFunds as $loanFund) {
                $investor = $loanFund->investor;
                $code = config('unicredit.flow')['loan_fund_rvsl'];
                $financeHandler->handleDouble(
                    $user, $investor, $loanFund->amount, $loanRequest, 'ETW', $code
                );
            }
            
            //update loan request
            $loanRequest->update([
                'status' => '4'
            ]);
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            return redirect()->back()->with('failure', 'An error occurred. Please try again');
        }
        
        return redirect()->back()->with('success', 'Loan Request Cancelled Successfully');
    }

    public function requestAuthorization($mandateId, $requestId)
    {
        $timestamp = Carbon::now()->toW3cString();
        $timestamp = $this->str_lreplace(':', '', $timestamp);
        $rid = time();
        $headers = array();
        $hash = hash('sha512', config('remita.apiKey') . $rid . config('remita.apiToken'));
        $headers[]='Accept: application/json';
        $headers[]='Content-Type: application/json';
        $headers[]='MERCHANT_ID: ' . config('remita.merchantId');
        $headers[]='API_KEY: ' . config('remita.apiKey');
        $headers[]='REQUEST_ID: ' . $rid;
        $headers[]='REQUEST_TS: ' . $timestamp;
        $headers[]='API_DETAILS_HASH: ' . $hash;

        $url = config('remita.baseUrl') . 'exapp/api/v1/send/api/echannelsvc/echannel/mandate/requestAuthorization';
        
        $fields = json_encode(array(
            'mandateId' => $mandateId, 
            'requestId' => $requestId, 
        ));
        
        $result = $this->makePostRequest($url, $headers, $fields);
        
        if($result && $result->statuscode == '00') {
            return view('users.remita_activation')->with([
                'authParams' => $result->authParams,
                'requestId' => $result->requestId,
                'mandateId' => $result->mandateId,
                'remitaTransRef' => $result->remitaTransRef
            ]);    
        } else if($result && $result->statuscode == '075') {
            return redirect()->back()->with('success', 'Mandate is already active');
        } else if($result && $result->statuscode == '02') {
            return redirect()->back()->with('failure', 'Authentication error. You might want to try manual activation');
        }
        
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function activateMandate(Request $request, FinanceHandler $financeHandler)
    {
        $timestamp = Carbon::now()->toW3cString();
        $timestamp = $this->str_lreplace(':', '', $timestamp);
        
        $rid = time();
        
        $headers = array();
        $hash = hash('sha512', config('remita.apiKey') . $rid . config('remita.apiToken'));
        $headers[]= 'Accept: application/json';
        $headers[]="Content-Type: application/json";
        $headers[]='MERCHANT_ID: ' . config('remita.merchantId');
        $headers[]='API_KEY: ' . config('remita.apiKey');
        $headers[]='REQUEST_ID: ' . $rid;
        $headers[]='REQUEST_TS: ' . $timestamp;
        $headers[]='API_DETAILS_HASH: ' . $hash;
        
        $url = config('remita.baseUrl') . 'exapp/api/v1/send/api/echannelsvc/echannel/mandate/validateAuthorization';
        
        $authParams = [];
        
        if($request->OTP) {
            array_push($authParams, ["param1" => "OTP", "value" => $request->OTP]);
        }
        
        if ($request->CARD){
            array_push($authParams, ["param2" => "CARD", "value" => $request->CARD]);
        } 
        
        $fields = json_encode(array(
            'remitaTransRef' => $request->remitaTransRef,
            'authParams' => $authParams
        ));
        
        $result = $this->makePostRequest($url, $headers, $fields);
        
        if($result->statuscode == '00') {
            //set up loan repayment and transfer loan
            $loanRequest = Auth::guard('web')->user()
                            ->loanRequests
                            ->where('mandateId', $request->mandateId)
                            ->where('requestId', $request->requestId)
                            ->first();
            
            $response = $this->setUpRepayment($loanRequest, $financeHandler);
            
            if($response['status']) {
                //update loan request
                $loanRequest->update(['mandateStage' => 2]);
                Session::flash('success', 'Loan has been successfully set up');
                return redirect()->route('users.loans.view', ['reference' => $response['reference']]);
            } else {
                $loanRequest->update(['mandateStage' => 3]);
                return redirect()->back()->with('failure', 'Mandate Activated. Failed to set up loan');
            }
        } 
        return redirect()->back()->with('failure', 'An error occurred. Please try again')->withInput();
    }
    
    public function verifyMandateStatus(LoanRequest $loanRequest, FinanceHandler $financeHandler)
    {
        $response = $this->checkMandateStatus($loanRequest, $financeHandler);
        
        if ($response['status'] === 'success' && $response['action'] === 1) {
            // redirect to loan page
            return redirect()->route('users.loans.view', ['reference' => $response['reference']]);
        }
        
        // return redirect back
        return redirect()->back()->with($response['status'], $response['message']);
    }
    
    private function getStartDate()
    {
        $now = Carbon::today();
        
        $startDate = Carbon::today();

        // start date is always 25th
        $startDate->day = 25;
        
        // if today is > 18, start month is next month
        if ($now->day > 18) {
            $startDate->addMonth();
        }
        
        return $startDate->format('d/m/Y');
    }
    
    private function getEndDate(LoanRequest $loanRequest)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $this->getStartDate());
        return $startDate->addMonths($loanRequest->duration)->format('d/m/Y');
    }
}
