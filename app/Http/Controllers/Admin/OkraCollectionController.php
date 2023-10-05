<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Okra\OkraService;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\RepaymentPlan;
use Carbon\Carbon;
use App\Models\OkraLog;
use App\Models\BankDetail;
use App\Models\OkraSetup;
use App\Helpers\FinanceHandler;
use App\Recipients\DynamicRecipient;
use App\Notifications\Investors\LoanToUpNotification;
use App\Notifications\Shared\DebitConfirmationNotification;
use App\Unicredit\Collection\RepaymentManager;

class OkraCollectionController extends Controller
{
    public function getOkraBorrowers(){
        $okraService = new OkraService(new Client);
        $users = User::with('banks')->get()->filter(function($users){ return isset($users->banks->last()->okra_account_id) && $users->banks->last()->okra_balance_id == null ;});
        $userBalance = User::with('banks')->get()->filter(function($userBalance){ return isset($userBalance->banks->last()->okra_account_id) && isset($userBalance->banks->last()->okra_balance_id) ;});
        $userPayment = User::with('okraSetup')->get()->filter(function($userPayment){ return isset($userPayment->okraSetup->last()->payment_id);});
        
        $today = Carbon::today();
        //$today = '2021-09-28';        
        $plans = RepaymentPlan::with('loan')
            ->where('status', false)
            ->where('payday', '<=', $today)            
            ->get()->filter(function($plans) {
            return optional($plans->loan)->collection_plan == 101;
        });
        return view('admin.okra-collections.view', compact('users', 'plans','userBalance','userPayment'));        
    }

    public function updateBalanceID(Request $request){
        $okraService = new OkraService(new Client);
        $reference = $request->reference;
        $user = User::whereReference($reference)->first();
        $bank = $user->banks->last();
            if(!$bank->okra_balance_id){
                $accountId = $bank->okra_account_id;
                $okraService->getBankAccountDetails($accountId);
                $accountInfo = $okraService->getResponse();
                $balanceID = $accountInfo['data']['accounts'][0]['balance'];
                $bank->update(['okra_balance_id'=> $balanceID]);
                if($bank->update(['okra_balance_id'=> $balanceID])){
                    return redirect()->back()->with('success', 'Balance ID Updated Successfully');
                } else {
                    return redirect()->back()->with('error', 'Opps Something Went Wrong, Please Try Again');
                } 
            }
    }

    public function retrieveBalance(Request $request){
            $okraService = new OkraService(new Client);                  
            // 1. Get due repaymentPlan                
            $balance_id = $request->okra_balance_id;  
            $bank_user = BankDetail::where('okra_balance_id',$balance_id)->first();
            $accountId = $bank_user->okra_account_id;          
            //refresh and get user account balance  
            
            $okraService->refreshBalance($accountId);         
            $okraService->checkBalance($balance_id);
            $balanceInfo = $okraService->getResponse();
            $balance = $balanceInfo['data']['balance']['available_balance'];
            if($balance){
                $bank_user->update(['account_balance'=>$balance]);
                return redirect()->back()->with('success', 'Balance is: '.$balance);
            }else{
                return redirect()->back()->with('error', 'Balance Retrieval Failed, Please Try Again');
            }            
    }
     public function opay(Request $request){
        $plan_id = $request->plan_id;              
        $plan = RepaymentPlan::where('id', $plan_id)->first();
        //bank Details
        $user = $plan->loan->user;
        $loan = $plan->loan;
        $bankDetails = $user->banks->last();            
        $debitAccnt = $bankDetails->okra_account_id;
        $balance = $bankDetails->account_balance;
        $creditAccnt = '60f008f23bd4932f2382ec8f';
        $currency = 'NGN';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.okra.ng/v2/pay/initiate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'account_to_debit'=>  $debitAccnt,                
                'account_to_credit'=> $creditAccnt,
                'amount'=> $plan->emi, 
                "currency"=> $currency                
            ]),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2MGMxZjBlMTQzZGY0YTMwMDRkOTZiN2MiLCJpYXQiOjE2MjMzMjYzMzh9.Ehym9v6JpUhZ2Pl1QxnUTXKl6-mkzWBYaXkb2WbbFUI",
                "apiKey: 5f4d65c2-9261-56df-9e26-30da82302f59",
                "baseUrl: https://api.okra.ng/v2/"
            ],
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) {
            // there was an error contacting the Paystack API
            return redirect()->back()->with('error', 'Payment Initiation Was Not Successful'.$err);
        }
        $tranx = json_decode($response, true);
        if (!$tranx['status']) {
            // there was an error from the API
            return redirect()->back()->with('error', 'Payment Initiation Was Not Successful'.$tranx['message']);
        }else{
            return redirect()->back()->with('success', 'Successful '.$tranx['message']);
        }
        
     }
    public function okraPayment(Request $request){
    try {
        $okraService = new OkraService(new Client);
        $plan_id = $request->plan_id;              
        $plan = RepaymentPlan::where('id', $plan_id)->first();
        //bank Details
        $user = $plan->loan->user;
        $loan = $plan->loan;
        $bankDetails = $user->banks->last();            
        $debitAccnt = $bankDetails->okra_account_id;
        $balance = $bankDetails->account_balance;
        $creditAccnt = '60f008f23bd4932f2382ec8f';
        $currency = 'NGN';
        if($balance >= $plan->emi){
            $amount = 100000;
            $okraService->initiatePayment($debitAccnt,$creditAccnt,$amount,$currency);
            $paymentDetails = $okraService->getResponse();  
            $paymentId = $paymentDetails['data']['payment']['id'];          
            if($paymentId){            
                OkraSetup::create(['user_id'=> $user->id,'bank_response'=>1,'credit_account'=>$creditAccnt,'debit_account'=>$debitAccnt,
                'payment_id'=>$paymentId,'setup_fee'=>$amount, 'status'=>1, 'plan_id'=>$plan_id, 'loan_id'=>$loan->id]);
                return redirect()->back()->with('success', 'Payment Initiation Was Successful, Please Verify Payment');
            }   
        }
    } catch(\Exception $e) {
            
        return redirect()->back()
                        ->with('error', 'the payment was not successful' . $e->getMessage() );
    }
        //$previousPayments = OkraLog::where('repayment_plan_id', $plan_id)->get();
        //$oldPayments = $previousPayments->sum('amount_paid');
        //$bal = $plan->emi - $oldPayments;
        // if($previousPayments && $balance >= $bal){
        //     $amount = $bal*100;
        //     $okraService->initiatePayment($debitAccnt,$creditAccnt,$amount,$currency);
        //     $paymentDetails = $okraService->getResponse();  
        //     $paymentId = $paymentDetails['data']['payment']['id'];          
        //     if($paymentId){            
        //         OkraSetup::create(['user_id'=> $user->id,'bank_response'=>1,'credit_account'=>$creditAccnt,'debit_account'=>$debitAccnt,
        //         'payment_id'=>$paymentId,'setup_fee'=>$amount/100,'status'=>1,'plan_id'=>$plan_id, 'loan_id'=>$loan->id]);
        //         return redirect()->back()->with('success', 'Payment Initiation Was Successful, Please Verify Payment');
        //     } else{
        //         return redirect()->back()->with('error', 'Payment Initiation Failed, Please Try Again'); 
        //     }
        // }
        // elseif($previousPayments && $balance < $bal && $balance > 0){
        //     $amount = $balance;
        //     $okraService->initiatePayment($debitAccnt,$creditAccnt,$amount,$currency);
        //     $paymentDetails = $okraService->getResponse();  
        //     $paymentId = $paymentDetails['data']['payment']['id'];          
        //     if($paymentId){            
        //         OkraSetup::create(['user_id'=> $user->id,'bank_response'=>1,'credit_account'=>$creditAccnt,'debit_account'=>$debitAccnt,
        //         'payment_id'=>$paymentId,'setup_fee'=>$amount/100, 'plan_id'=>$plan_id, 'loan_id'=>$loan->id]);
        //         return redirect()->back()->with('success', 'Payment Initiation Was Successful, Please Verify Payment');
        //     } else{
        //         return redirect()->back()->with('error', 'Payment Initiation Failed, Please Try Again'); 
        //     }
        // }
        
        // else{
        //     return redirect()->back()->with('error', 'Insufficient Fund'); 
        // } 
        // elseif(!$previousPayments && $balance < $plan->emi && $balance > 0){
        //     $amount = $balance*100;
        //     $okraService->initiatePayment($debitAccnt,$creditAccnt,$amount,$currency);
        //     $paymentDetails = $okraService->getResponse();  
        //     $paymentId = $paymentDetails['data']['payment']['id'];          
        //     if($paymentId){            
        //         OkraSetup::create(['user_id'=> $user->id,'bank_response'=>1,'credit_account'=>$creditAccnt,'debit_account'=>$debitAccnt,
        //         'payment_id'=>$paymentId, 'setup_fee'=>$amount/100, 'plan_id'=>$plan_id, 'loan_id'=>$loan->id]);
        //         return redirect()->back()->with('success', 'Payment Initiation Was Successful, Please Verify Payment');
        //     } else{
        //         return redirect()->back()->with('error', 'Payment Initiation Failed, Please Try Again'); 
        //     }  
        // }  
    }

    public function verifyOkraPayment(Request $request){
        $okraService = new OkraService(new Client);
        $paymentId = $request->payment_id; 
        $okraService->verifyPayment($paymentId);  
        $paymentDetails = $okraService->getResponse(); 
        $message = $paymentDetails['status'];
        if($message){
            return redirect()->back()->with('success', 'Okra Payment Verification Was Successfully, Check OkraLogs To See Transaction Info');
        }else{
            return redirect()->back()->with('error', 'Okra Payment Verification Failed, Please Try Again');
        }
    }

    public function payInvestors(Request $request){        
        $repaymentManager = new RepaymentManager();
        $plan_id = $request->plan_id;
        $plan = RepaymentPlan::where('id', $plan_id)->first(); 
        //bank Details
        $user = $plan->loan->user;
        $loan = $plan->loan;
        $okraSetup = OkraSetup::where('user_id', $user->id)->latest()->first();
        if($okraSetup->status){
            $nextPlan = $loan->repaymentPlans()->whereStatus(false)->first();           
            if (!$nextPlan) return 'Plan not found';
            $nextPlan->update([
                    'status' => true, 
                    'date_paid' => Carbon::now()
            ]);
            $repaymentManager->settleInvestors($loan, $plan);                
            if($loan->repaymentPlans()->whereStatus(false)->count() < 1) {
                $loan->update(['status' => 2]);
            }
            try{
                // Notify Admin and Borrower  
                $loan->user->notify(new DebitConfirmationNotification($plan));
                $adminEmail = config('unicredit.admin_email');
                if ($adminEmail){
                    $admin = new DynamicRecipient($adminEmail);
                    $admin->notify(new DebitConfirmationNotification($plan));
                }
            } catch(\Exception $e){

            }    
            OkraLog::create([
                'user_id' => $user->id,
                'repayment_plan_id'=> $plan->id,
                'loan_id'=> $loan->id,
                'emi'=> $plan->emi,
                'amount_paid'=>$amount,  
                'status'=> $status,          
                'date_paid'=>Carbon::now()
            ]);
        }else{
            OkraLog::create([
                'user_id' => $user->id,
                'repayment_plan_id'=> $plan->id,
                'loan_id'=> $loan->id,
                'emi'=> $plan->emi,
                'amount_paid'=>$amount,
                'date_paid'=>Carbon::now()
            ]);
        }
        
    }

    public function okraRepayment(Request $request)
    {
        $okraService = new OkraService(new Client);
        $repaymentManager = new RepaymentManager();      
        // 1. Get due repaymentPlan                
        $plan_id = $request->plan_id;              
        $plan = RepaymentPlan::where('id', $plan_id)->first();
        //bank Details
        $user = $plan->loan->user;
        $loan = $plan->loan;
        $bankDetails = $user->banks->last();            
        $debitAccnt = $bankDetails->okra_account_id; 
        $balanceId =  $bankDetails->okra_balance_id;            
        $creditAccnt = '60f008f23bd4932f2382ec8f';
        $currency = 'NGN';
        //refresh and get user account balance
        $okraService->refreshBalance($debitAccnt);
        $okraService->checkBalance($balanceId);
        $balanceInfo = $okraService->getResponse();
        $balance = $balanceInfo['data']['balance']['available_balance'];
        //confirm okra debit account exist and funds in account
        if($debitAccnt && $balance){
            $previousPayments = OkraLog::where('repayment_plan_id', $plan_id)->get();            
            if($previousPayments){
                $oldPayments = $previousPayments->sum('amount_paid');  
                if($plan->emi > $oldPayments){
                    $emiAmnt = $plan->emi - $oldPayments;
                    $amount = $emiAmnt * 100;
                    //if balance is greater or equal to emi then initiate and verify payment
                    if($balance >= $amount){
                        $status = 1;
                        if($this->initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status)){
                            $this->createOkraLog($user, $plan, $loan, $amount, $status);
                            return redirect()->back()->with('success', 'Okra Payment Was Successfully, Check OkraLogs To See Transaction Info');
                        }
                    }
                    //checkif balance is greater or equal to half of emi then initiate and verify payment
                    if($balance < $amount && $balance > 0){
                        $amount = $balance;
                        $status = 0;
                        if($this->initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status)){
                            $this->createOkraLog($user, $plan, $loan, $amount, $status);
                            return redirect()->back()->with('success', 'Okra Payment Was Successfully, Check OkraLogs To See Transaction Info');
                        }
                    } else{
                        return redirect()->back()->with('error', 'Insufficient Fund');
                    }               
                }
            }
            if(!$previousPayments){           
                $emiAmnt = $plan->emi;
                $amount = $emiAmnt * 100;
                //if balance is greater or equal to emi then initiate and verify payment 
                if($balance >= $amount){
                    $status = 1;
                    if($this->initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status)){
                        $this->createOkraLog($user, $plan, $loan, $amount, $status);
                        return redirect()->back()->with('success', 'Okra Payment Was Successfully, Check OkraLogs To See Transaction Info');
                    }
                    
                }
                //checkif balance is greater or equal to half of emi then initiate and verify payment
                if($balance < $amount && $balance > 0){
                    $amount = $balance;
                    $status = 0;
                    if($this->initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status)){
                        $this->createOkraLog($user, $plan, $loan, $amount, $status);    
                        return redirect()->back()->with('success', 'Okra Payment Was Successfully, Check OkraLogs To See Transaction Info');                        
                    }
                    
                }
                else{
                    return redirect()->back()->with('error', 'Insufficient Fund');
                }                              
            }
                            
        }else{
            return redirect()->back()->with('error', 'Unable To Retrieve Account Balance');
        }
    }

  
    public function initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status){
        $okraService = new OkraService(new Client);
        $repaymentManager = new RepaymentManager();
        $okraService->initiatePayment($debitAccnt,$creditAccnt,$amount,$currency);
        $paymentDetails = $okraService->getResponse();            
        if($paymentDetails){
            $paymentId = $paymentDetails['data']['payment']['id'];
            $okraService->verifyPayment($paymentId);  
            $paymentDetails = $okraService->getResponse(); 
            $message = $paymentDetails['status'];
            if($message) {
                return true;
            }
        }
    }

    public function createOkraLog($user, $plan, $loan, $amount, $status){
        $repaymentManager = new RepaymentManager();        
        if($status){           
                $nextPlan = $loan->repaymentPlans()->whereStatus(false)->first();           
                if (!$nextPlan) return 'Plan not found';
                $nextPlan->update([
                        'status' => true, 
                        'date_paid' => Carbon::now()
                    ]);
                $repaymentManager->settleInvestors($loan, $plan);                
                if($loan->repaymentPlans()->whereStatus(false)->count() < 1) {
                    $loan->update(['status' => 2]);
                }
                try {
                    // Notify Admin and Borrower  
                    $loan->user->notify(new DebitConfirmationNotification($plan));
                    $adminEmail = config('unicredit.admin_email');
        
                    if ($adminEmail) {
                        $admin = new DynamicRecipient($adminEmail);
                        $admin->notify(new DebitConfirmationNotification($plan));
                    }
                } catch (\Exception $e) {

                }
            
                OkraLog::create([
                    'user_id' => $user->id,
                    'repayment_plan_id'=> $plan->id,
                    'loan_id'=> $loan->id,
                    'emi'=> $plan->emi,
                    'amount_paid'=>$amount,  
                    'status'=> $status,          
                    'date_paid'=>Carbon::now()
                ]);
            }
            
        
            if(!$status){
                OkraLog::create([
                    'user_id' => $user->id,
                    'repayment_plan_id'=> $plan->id,
                    'loan_id'=> $loan->id,
                    'emi'=> $plan->emi,
                    'amount_paid'=>$amount,  
                    'status'=> $status,          
                    'date_paid'=>Carbon::now()
                ]);
            }
    }

    public function okraRecords(){
        $okraLogs = OkraLog::all();
        return view('admin.okra-collections.payment-records', compact('okraLogs'));  
    }
}
