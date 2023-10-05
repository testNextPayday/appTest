<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;
use App\Http\Controllers\Controller;
use App\Unicredit\Contracts\CardSweeper;
use App\Unicredit\Collection\CardService;
use App\Services\TransactionVerificationService;

class ManagedLoanSweeperController extends Controller
{
    //
    
    /**
     * This returns a view to access sweeps
     *
     * @return void
     */
    public function index()
    {
        return view('admin.loans.manage-sweeps');
    }
    
    /**
     * Lets all plans that are sweepable and due for sweepable
     *
     * @param  mixed $request
     * @return void
     */
    public function getPlans(Request $request)
    {
        $today  = Carbon::now()->toDateTimeString();

        $loans = Loan::all()->filter(
            function ($loan) { 
                return $loan->sweepEnabled;
            }
        );

        $loanIds = $loans->pluck('id')->toArray();

        $query = RepaymentPlan::whereIn('loan_id', $loanIds)->with('buffers');

        $plansQuery = $query->where('status', false)->where('payday', '<', $today);

        $plans = $plansQuery->with('loan.user')->get();

        return response()->json($plans);
    }

    
    /**
     * We use an instance of the card sweeper to sweep loans
     *
     * @return void
     */
    public function sweepPlan(
        Request $request, RepaymentPlan $plan, 
        CardService $sweeper, TransactionVerificationService $verifyService
    )
    {
        try {

            $maxBatch = PaymentBuffer::max('batch');

            // The batch no for this sweep
            $batch = $maxBatch + 1;

            session(['batch'=> $batch]);
            
            $attempt = $sweeper->attemptInBits($plan);

            // refresh to get the latest updates
            $plan->refresh();

            // verify each of the payment on the fly
            foreach ($plan->buffers as $buffer) {

                $verifyService->verifyBuffer($buffer);
            }

            // verify the plan by updating
            $verifyService->verifyRepaymentPlanOnBuffers($plan);

        } catch(\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json('No error while sweeping');
    }

    
    /**
     * We get the status of the current sweeping batch
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getStatus(Request $request)
    {
        $batch = session()->get('batch');

        if (! isset($batch)) {

            throw new \Exception('No batch is set. cannot get status'); 
        }

        $buffers = PaymentBuffer::where('batch', $batch)->get();

        $success = $buffers->where('status', 1)->count();

        $failed = $buffers->where('status', 0)->count();

        $statusArray = [
            'failed'=> $failed.' has failed',
            'success'=> $success.' has completed successfully'
        ];

        return response()->json($statusArray);
    }
}
