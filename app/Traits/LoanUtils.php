<?php

namespace App\Traits;

use App\Models\LoanRequest;
use App\Models\Loan;
use App\Models\Settings;
use App\Models\User;
use App\Models\Fee;
use App\Models\RepaymentPlan;

use App\Traits\Accounting;
use App\Traits\Utilities;
use App\Helpers\FinanceHandler;

use App\Notifications\Users\LoanAcceptedNotification;

use Carbon\Carbon;
use DB;

trait LoanUtils {
    
    use Accounting, Utilities;
    
    public function getStartDate()
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
    
    public function getEndDate(LoanRequest $loanRequest)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $this->getStartDate());
        return $startDate->addMonths($loanRequest->duration - 1)->format('d/m/Y');
    }
    
    public function calculateRepayment(LoanRequest $loanRequest) 
    {
        $funds = $principal = $loanRequest->funds()->sum('amount');
        $management_fee = $loanRequest->fee($principal);
        $duration = $loanRequest->duration;
        $interestPercentage = $loanRequest->interest_percentage;
        $rateOfInterest = ($interestPercentage/100) * $duration;
        
        $rate = $interestPercentage/100;
        $emi = $this->getFlatEmi($rate, $principal, $duration);
        //$emi = $this->pmt($principal,$rate,$duration);
        $monthly_interest = $this->getInterest($rate, $principal, $duration);
        $monthly_principal = $this->getPrincipal($principal, $duration);
        
        $monthly_payment = ceil($emi + $management_fee);
        
        return [
            'principal' => $funds,
            'management_fee' => $management_fee,
            'emi' => $emi,
            'monthly_payment' => $monthly_payment,
            'rateOfInterest' => $rateOfInterest,
            'monthly_principal' => $monthly_principal,
            'monthly_interest' => $monthly_interest
        ];
    }
    
    public function setUpMandate(User $user, $monthly_payment, LoanRequest $loanRequest)
    {
        
        $start_date = $this->getStartDate();
        $end_date = $this->getEndDate($loanRequest);
        
        $merchantId = config('remita.merchantId');
        $serviceTypeId = config('remita.serviceTypeId');
        
        $requestId = time();
        $apiKey = config('remita.apiKey');
        
        $bank = $user->bankDetails()->latest()->first();
        if($bank) {
            $payerBankCode = $bank->bank_code;
            $payerAccount = $bank->account_number;
        } else {
            return ['status' => false, 'message' => 'Bank details unavailable'];
        }
        
        $amt = $monthly_payment;
        $hashParams = $merchantId.$serviceTypeId.$requestId.$amt.$apiKey;
        $hash = hash('sha512', $hashParams);
        
        $fields = json_encode(array(
            'merchantId' => $merchantId, 
            'serviceTypeId' => $serviceTypeId, 
            'hash' => $hash,
            'payerName' => $user->name,
            'payerEmail' => $user->email,
            'payerPhone' => $user->phone,
            'payerBankCode' => $payerBankCode,
            'payerAccount' => $payerAccount, 
            "requestId" => $requestId, 
            "amount" => $monthly_payment,
            "startDate" => $start_date,
            "endDate" => $end_date,
            "mandateType" => "DD",
            "maxNoOfDebits" => $loanRequest->duration,
        ));
        
        $headers = array();
        // $header[] = 'Authorization: Bearer ' . config('unicredit.sharenet_token');
        $headers[]= 'Accept: application/json';
        $headers[]="Content-Type: application/json";
        $url =  config('remita.baseUrl') . 'exapp/api/v1/send/api/echannelsvc/echannel/mandate/setup';
        
        $result = $this->makePostRequest($url, $headers, $fields);
        
        if($result && $result->statuscode == '040') {
            $mandateId = $result->mandateId;
            
            //store mandateId and requestId
            $loanRequest->update(['mandateId' => $mandateId, 'requestId' => $requestId, 'mandateStage' => 1]);

            $hash = hash('sha512', $merchantId.$apiKey.$requestId);
            
            $redirectUrl = config('remita.baseUrl') . "ecomm/mandate/form/";
            $redirectUrl .= $merchantId ."/". $hash . "/" .$mandateId ."/" .$requestId ."/rest.reg";
            
            return [
                'status' => true, 
                'redirectUrl' => $redirectUrl, 
                'mandateId' => $mandateId, 
                'requestId' => $requestId
            ];    
        }
        return ['status' => false];
    }
    
    public function checkMandateStatus(LoanRequest $loanRequest, FinanceHandler $financeHandler)
    {
        $requestId = $loanRequest->requestId;
        $merchantId = config('remita.merchantId');
        $hash = hash('sha512', $requestId . config('remita.apiKey') . $merchantId);
        $url = config('remita.baseUrl') . "ecomm/mandate/{$merchantId}/{$requestId}/{$hash}/status.reg";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->jsonp_decode($result);
        
        $response['action'] = 0;
        if($result && @$result->isActive) {
            if($loanRequest->mandateStage != 2) {
                $gtResponse = $this->setUpRepayment($loanRequest, $financeHandler);
            
                if($gtResponse['status']) {
                    //update loan request
                    $loanRequest->update(['mandateStage' => 2]);
                    $response['status'] = 'success';
                    $response['message'] = 'Loan has been successfully set up';
                    $response['action'] = 1; // Loan set up, redirect to loan page
                    $response['reference'] = $gtResponse['reference'];
                    return $response;
                } 
                
                $loanRequest->update(['mandateStage' => 3]);
                $response['status'] = 'failure';
                $response['message'] = 'Mandate Activated. Failed to set up loan';
                return $response;
            }
            
            $response['status'] = 'success';
            $response['message'] = 'Mandate is active.';
            return $response;
        }  
        
        $response['status'] = 'failure';
        $response['message'] = 'Mandate has not been activated';
        return $response;
    }
    
    public function setUpRepayment(LoanRequest $loanRequest, FinanceHandler $financeHandler)
    {
        $user = $loanRequest->user;
        $data = $this->calculateRepayment($loanRequest);
        $dueDate = Carbon::createFromFormat('d/m/Y', $this->getStartDate())->addMonths($loanRequest->duration);
        
        try {
            DB::beginTransaction();
            
            //update user wallet 
            //insurance = 2.5% of principal
            $insurance = 0.025 * $data['principal'];
            
            $code = config('unicredit.flow')['loan_acceptance'];
            $financeHandler->handleDouble(
                $user, $user, $data['principal'], $loanRequest, 'ETW', $code
            );
            
            $code = config('unicredit.flow')['insurance_fee'];
            $financeHandler->handleSingle(
                $user, 'debit', $insurance, null, 'W', $code
            );
            
            //update loan request
            $loanRequest->update(['status' => '4']);
            
            //activate all loan funds
            $funds = $loanRequest->funds;
            foreach($funds as $fund) {
                $fund->update(['status' => 2]);
            }
            
            //create loan
            $loan = new Loan();
            //$reference = $loan->generateReference();
            $loanData = [
              //  'reference' => $reference,
                'user_id' => $user->id,
                'amount' => $data['principal'],
                'interest_percentage' => $loanRequest->interest_percentage,
                'due_date' => $dueDate,
                'request_id' => $loanRequest->id,
                'collection_plan' => $loanRequest->getOriginal('collection_plan')
            ];
            
            if ($loanRequest->staff_id) {
                $loanData['collector_type'] = "App\Models\Staff";
                $loanData['collector_id'] = $loanRequest->staff_id;
            }
            
            $loan = Loan::create($loanData);
            $loan->update(['emi' => $data['emi']]);
            
            //set up repayment plans
            $month_number = 0;
            $principal_balance = $data['principal'];
            $duration = $loanRequest->duration;
            $tenure = $duration;
            while($duration > 0) {
                
                $interest = $data['monthly_interest'];
                $principal_part = $data['monthly_principal'];
                
                $principal_balance = ceil($principal_balance - $principal_part);
                $startDate = Carbon::createFromFormat('d/m/Y', $this->getStartDate());
                RepaymentPlan::create([
                    'loan_id' => $loan->id, 
                    'interest' => $interest, 
                    'principal' => $principal_part,
                    'emi' => $data['emi'],
                    'management_fee' => $data['management_fee'], 
                    'balance' => $principal_balance, 
                    'month_no' => ++$month_number, 
                    'payday' => $startDate->addMonths($month_number)->format('Y-m-d'),
                    'status' => false
                ]);
                $duration = $duration - 1;
            }
            
            DB::commit();
            
            $user->notify(new LoanAcceptedNotification($loan));
        } catch(Exception $e) {
            DB::rollback();
            return ['status' => false];
        }
        
        return ['status' => true, 'reference' => $loan->reference];
    }
}