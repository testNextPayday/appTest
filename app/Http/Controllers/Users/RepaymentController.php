<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Models\RepaymentPlan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\LoanRepaymentService;

class RepaymentController extends Controller
{
    //

    
    /**
     * Takes a loan and the amount paid
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Loan $loan
     * @return void
     */
    public function updatePlans(Request $request, LoanRepaymentService $collectService)
    {
        $paymentIds = $request->paymentIds;
        
        try{

            DB::beginTransaction();

            foreach ($paymentIds as $index=>$id) {
            
                $plan = RepaymentPlan::find($id);

                if ($plan) {
    
                    $collectService->makeSuccessfulPlanCollection($plan);
                } else {

                    throw new \InvalidArgumentException("Invalid plan sent for update");
                }

            }

            DB::commit();

            return response()->json('Plans where successfull updated', 200);

        }catch (\Exception $e) {
           
            DB::rollback();

            return response()->json($e->getMessage(), 422);
        }

    }
}
