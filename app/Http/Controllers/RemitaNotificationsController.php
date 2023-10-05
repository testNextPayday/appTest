<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\RepaymentPlan;
use App\Models\LoanRequest;
use App\Helpers\LoanCollector;
use DB;
use Carbon\Carbon;

class RemitaNotificationsController extends Controller
{
    public function loanRepayment(Request $request, LoanCollector $collector)
    {
        $data = $request->all();
        
        // customerId, payment_date, amount, payment_status, mandateRef
        $loanRequest = LoanRequest::where('mandateId', @$data['mandateRef'])->first();
        
        if (!$loanRequest || 
            strtolower($data['payment_status']) !== 'paid' || 
            !($loan = $loanRequest->loan)) return 'Request not found';
            // find loan
        $nextPlan = $loan->repaymentPlans()->whereStatus(false)->first();
           
        if (!$nextPlan) return 'Plan not found';
                
        DB::beginTransaction();
            
        try {
            $nextPlan->update([
                'status' => true, 
                'date_paid' => Carbon::parse($data['payment_date'])->toDateString()
            ]);
                
            $collector->settleInvestors($loan, $loanRequest, $nextPlan);
                
            if($loan->repaymentPlans()->whereStatus(false)->count() < 1) {
                $loan->update(['status' => 2]);
            }
            
            DB::commit();
            
            return response()->json([
                
                "response_code" => "00",
                "response_descr" => "Request ack OK",
                "ack_id" => time()
        
            ], 200);
            
        } catch(Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        
    }
}