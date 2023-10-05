<?php

namespace App\Console\Commands\Repayments;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\RepaymentPlan;
use App\Models\OkraLog;
use GuzzleHttp\Client;
use App\Services\Okra\OkraService;
use App\Helpers\FinanceHandler;
use App\Recipients\DynamicRecipient;
use App\Notifications\Investors\LoanToUpNotification;
use App\Notifications\Shared\DebitConfirmationNotification;
use App\Unicredit\Collection\RepaymentManager;
class OkraDDM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'okra:ddm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $okraService = new OkraService(new Client);
        $repaymentManager = new RepaymentManager();      
        // 1. Get all due repaymentPlans        
        $plans = $this->getPlans();                   
        
        // 2. Loop through repayment plans
        foreach ($plans as $plan){
            $loan = $plan->loan;
            $planID = $plan->id;          

            //bank Details
            $user = $plan->loan->user;
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

                $previousPayments = OkraLog::where('repayment_plan_id', $planID)->get();            
                if($previousPayments){
                    $oldPayments = $previousPayments->sum('amount_paid');  
                    if($plan->emi > $oldPayments){
                        $emiAmnt = $plan->emi - $oldPayments;
                        $amount = $emiAmnt * 100;
                        //if balance is greater or equal to emi then initiate and verify payment 
                        
                        if($balance >= $amount){
                            $status = 1;
                            $this->initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status);
                            $this->createOkraLog($user, $plan, $loan, $amount, $status);
                        }
                        //checkif balance is greater or equal to half of emi then initiate and verify payment
                        if($balance < $amount && $balance > 0){
                            $amount = $balance;
                            $status = 0;
                            $this->initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status);
                            $this->createOkraLog($user, $plan, $loan, $amount, $status);                            
                        }                
                    }
                }
                if(!$previousPayments){           
                    $emiAmnt = $plan->emi;
                    $amount = $emiAmnt * 100;
                    //if balance is greater or equal to emi then initiate and verify payment 
                    if($balance >= $amount){
                        $status = 1;
                        $this->initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status);
                        $this->createOkraLog($user, $plan, $loan, $amount, $status);
                    }
                    //checkif balance is greater or equal to half of emi then initiate and verify payment
                    if($balance < $amount && $balance > 0){
                        $amount = $balance;
                        $status = 0;
                        $this->initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status);
                        $this->createOkraLog($user, $plan, $loan, $amount, $status);                        
                    }                            
                }
            }                   
        }
    }

    /**
     * Get plans that are to be swept
     *
     * @return void
     */
    protected function getPlans()
    {
        $today = Carbon::today();
        //$today = '2021-09-28';        
        $plans = RepaymentPlan::with('loan')
            ->where('status', false)
            ->where('payday', '<=', $today)            
            ->get();
        return $plans->filter(function($plan) {
            return optional($plan->loan)->collection_plan == 101;
        });
    }

    protected function initiateVerifyPayment($debitAccnt,$creditAccnt,$amount,$currency, $status){
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
}
