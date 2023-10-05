<?php

namespace App\Http\Controllers\Admin\Repayments;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RepaymentPlan;
use App\Helpers\PlanUnconfirm;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LoanWalletTransaction;
use App\Services\LoanRepaymentService;

class LoanRepaymentController extends Controller
{
    public function index()
    {
        $plans = RepaymentPlan::where('status', 1)->whereHas('loan')->with('loan.user')->paginate(200);
        return view('admin.repayments.index', compact('plans'));
    }
    public function bulk()
    {

        return view('admin.repayments.bulk');
    }


    public function bulkRepayment(Request $request, LoanRepaymentService $collectService){
        
        try {
            DB::beginTransaction();
            $collectService->makeBulkUploads($request->all());
            DB::commit();

        }catch(\Exception $e) {
            DB::rollback();
            if(app()->environment() == 'local') {
                return response()->json(['failure' => $e->getMessage()], 422);
            }
            return response()->json(['failure' => $e->getMessage()], 422);
            //return response()->json(['failure' => 'An Error Ocuured. Please refresh and upload only failed'], 422);
        }

        return response()->json(['success' => 'You have successfully added bulk repayments. Please refresh'], 200);
        
    }

    public function approve()
    {
        //$plans = RepaymentPlan::where('has_upload', 1)->orWhere('collection_mode', '!=', '')->where('status', 0)->get();
        $pendingUploads = LoanWalletTransaction::logged()->get();
        return view('admin.repayments.approve', compact('pendingUploads'));
    }
    public function getBorrowers()
    {
        // Where not on penalties
        $users = Loan::active()->where('is_penalized', 0)->whereHas('repaymentPlans', function ($query) {

            return $query->where('status', 0);

        })->with([
            'user'=> function($query){
                $query->select('id', 'name');
            }, 
            'repaymentPlans' => function ($query) {
                $query->select('month_no', 'emi', 'id', 'loan_id')->where('status', 0);
            }
        ])->get(['id', 'reference', 'user_id']);

        return $users;
    }
    
    /**
     * Get pay from wallet data
     *
     * @param  mixed $request
     * @return void
     */
    public function getPayFromWalletData(Request $request)
    {

        $loan =  Loan::whereReference($request->reference)->with('user', 'repaymentPlans')->first();

        return response()->json($loan);
    }

    
    /**
     * Initiates a payment from wallet to a a plan
     *
     * @param  mixed $request
     * @return void
     */
    public function payFromWallet(Request $request, LoanRepaymentService $collectService)
    {
        try {
            $plan = RepaymentPlan::find($request->planID);
            if (! $plan) {
                throw new \Exception('Plan not found');
            }
            $collectService->makePaymentFromWallet($plan);

            return response()->json('Success');

        }catch(\Exception $e) {
            return response()->json($e->getMessage(), 422);
        }
    }
    
    public function unconfirm($id, LoanRepaymentService $loanRepaymentService)
    {
        $plan = RepaymentPlan::find($id);
        try {

            $loanRepaymentService->makePaymentUnconfirmation($plan);
            
            return redirect()->back()->with('success', 'Confirmation successful');
            
        } catch (\Exception $e) {
           
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }

    
    public function confirm(Request $request, LoanRepaymentService $loanRepaymentService)
    {
        try {
           
            DB::beginTransaction();
                $plan = RepaymentPlan::find($request->id);
        
                $collection_method = isset($request->collection_method) ? $request->collection_method : $plan->collection_method;
                session(['payment_method'=>$collection_method]);
                $loanRepaymentService->makePaymentFromWallet($plan);
            DB::commit();

            return redirect()->back()->with('success', 'Confirmation successful');

        } catch (\Exception $e) {

            DB::rollback();

            return redirect()->back()->with('failure', $e->getMessage());
        }

    }

    public function approveAll(Request $request, LoanRepaymentService $collectService){

        try {
           DB::beginTransaction();
          
            $logs = explode(",", $request->repayments[0]);
            
            $collectService->makeBulkApprovals($logs);
        
            DB::commit();

        }catch(\Exception $e) {
           DB::rollback();
           
            return redirect()->back()->with('failure', $e->getMessage());
        }


        return redirect()->back()->with('success','Repayments Approved successfully');

    }


    public function deleteAll(Request $request, LoanRepaymentService $collectService){

        try {
           DB::beginTransaction();
          
            $logs = explode(",", $request->delete_repayments[0]);
            
            $collectService->makeBulkDeletions($logs);
        
            DB::commit();

        }catch(\Exception $e) {
           DB::rollback();
            
            return redirect()->back()->with('failure', $e->getMessage());
        }


        return redirect()->back()->with('success','Repayments Deleted successfully');

    }

    public function repay($plan)
    {
        $repayment = RepaymentPlan::find($plan);
        try {
            $paidAmount = $repayment->paid_amount;

            if (isset($paidAmount) && (round($paidAmount, 2) < round($repayment->emi, 2))) {
                return;
            }
            $repayment->update([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }
    public function delete($id)
    {
        $repayment  = RepaymentPlan::find($id);
        $repayment->delete();
        return redirect()->back()->with('success', 'Repayment deleted successfully');
    }
   


   
}
