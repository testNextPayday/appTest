<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RepaymentPlan;
use App\Models\Loan;

use App\Traits\EncryptDecrypt;
use App\Remita\DAS\Collector;
use App\Helpers\DebitOrderIssuer;
use App\Helpers\LoanCollector;

use Carbon\Carbon;
use DB;

class LoanRepaymentController extends Controller
{
    use EncryptDecrypt;
    
    public function dueToday()
    {
        $today = Carbon::today();
        $plans = RepaymentPlan::whereStatus(false)
                ->where('payday', $today)
                ->latest()->get();
        return view('admin.repayments', compact('plans'));
    }

    public function insight()
    {
        return view('admin.repayments.insight');
    }
    
    public function dueCurrentMonth()
    {
        $today = Carbon::today();
        
        if (request('month')) {
            $month = request('month');
        } else {
            $month = $today->month;    
        }
        
        if (request('year')) {
            $year = request('year');
        } else {
            $year = $today->year;    
        }if(request('collection_mode')){
            $method = request('collection_mode');
        }else{
            $method = null;
        }
        
        $plans = RepaymentPlan::whereMonth('payday', $month)
                ->whereYear('payday', $year)
                ->where('collection_mode',$method)->latest()->get()->each(function($item){
                    $item->due_amount = $item->is_new ? $item->emi : $item->interest + $item->principal;
                })->filter(function($item){
                    return isset($item->loan) && $item->loan->isActive();
                });
                
        return view('admin.repayments.due-month', compact('plans', 'year', 'month','method'));
    }
    
    public function overdue()
    {
        $today = Carbon::today();
        $plans = RepaymentPlan::whereStatus(false)
                ->where('payday','<', $today)
                ->latest()->get()->each(function($item){
                    $item->due_amount = $item->is_new ? $item->emi : $item->interest + $item->principal;
                })->filter(function($item){
                    return isset($item->loan) && $item->loan->isActive();
                });
        return view('admin.repayments.index', compact('plans'));
    }
    
    public function unpaid()
    {
        $today = Carbon::today();
        $plans = RepaymentPlan::whereStatus(false)
                ->where('payday', '>', $today)
                ->latest()->get()->each(function($item){
                    $item->due_amount = $item->is_new ? $item->emi : $item->interest + $item->principal;
                })->filter(function($item){
                    return isset($item->loan) && $item->loan->isActive();
                });
        return view('admin.repayments.index', compact('plans'));
    }
    
    public function paid()
    {
        $today = Carbon::today();
        $plans = RepaymentPlan::whereStatus(true)
                ->latest()->get()->each(function($item){
                    $item->due_amount = $item->is_new ? $item->emi : $item->interest + $item->principal;
                })->filter(function($item){
                    return isset($item->loan) && $item->loan->isActive();
                });;
        return view('admin.repayments.index', compact('plans'));
    }

    public function issueDirectDebitOrder($plan_id)
    {
        $plan_id = $this->basicDecrypt($plan_id);
        if(!$plan_id) abort(404);
        
        $today = Carbon::today();
        
        $plan = RepaymentPlan::find($plan_id);
        if(!$plan) return redirect()->back()->with('failure', 'Repayment Plan not found');
        if($plan->status) return redirect()->back()->with('failure', 'Repayment made already');
        if($today < $plan->payday) return redirect()->back()->with('failure', 'Repayment not due');
        
        $response = DebitOrderIssuer::init()->issueDebitOrder($plan);
        
        if($response['status']) {
            return redirect()->back()->with('success', 'Repayment order approved');
        } 
        
        return redirect()->back()->with('failure', $response['message']);
    }
    
    public function stopCollection(Collector $collector, LoanCollector $loanCollector, Loan $loan)
    {
        $response = $collector->stopCollection($loan);
        
        if ($response->status && strtolower($response->status) === 'success') {
            // order passed
            $plans = $loan->repaymentPlans()->whereStatus(false)->get();
            
            $now = now()->toDateString();
            
            DB::beginTransaction();
            
            try {
            
                foreach($plans as $plan) {
                    $plan->update(['status' => true, 'date_paid' => $now ]);
                    $loanCollector->settleInvestors($loan, $loan->loanRequest, $plan);
                }
                        
                $loan->update(['status' => 2]);
                                
                DB::commit();
                        
                return redirect()->back()->with('success', 'Loan fulfilled successfully');
            } catch(Exception $e) {
                DB::rollback();
                return redirect()->back()->with('failure', 'Loan could not be fulfilled. ' . $e->getMessage());
            }
            
        }
        
        return redirect()->back()->with('failure', $response->responseMsg);
    }
}
