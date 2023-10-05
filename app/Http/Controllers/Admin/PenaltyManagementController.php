<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\Employer;
use App\Models\PenaltyEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\BuildEmployerPenalties;
use App\Jobs\DissolveEmployerPenalties;
use App\Services\Penalty\PenaltyService;
use App\Services\Penalty\BuildPenaltyService;
use App\Services\Penalty\AccruePenaltyService;
use App\Http\Resources\LoanPenaltyDetailsResource;
use App\Console\Commands\DissolvePreExistingPenalties;

class PenaltyManagementController extends Controller
{
    //    
    /**
     * Retrieve penalty details of a loan
     *
     * @param  mixed $request
     * @return void
     */
    public function getPenaltyDetails(Request $request)
    {
        $reference = $request->reference;

        $loan = Loan::whereReference($reference)->first();

        $penalDetails = new LoanPenaltyDetailsResource($loan);

        return response()->json($penalDetails);
    }

    
   
    
    /**
     * Dissolve Penalties on a particular loan
     *
     * @param  mixed $request
     * @param  mixed $loan
     * @param  mixed $penaltyService
     * @return void
     */
    public function dissolvePenalty(Request $request, Loan $loan, PenaltyService $penaltyService)
    {
        try {
            
            DB::beginTransaction();

            $penaltyService->dissolvePenalty($loan);

            DB::commit();

            return redirect()->back()->with('success', 'All Penalties dissolved');
        }catch (\Exception $e) {
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }

     /**
     * Dissolve Penalties on an employer
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Employer $employer
     * @return \Illuminate\Http\Response
     */
    public function dissolvePenaltyEmployer(Request $request, Employer $employer)
    {
        try {
           
            DissolveEmployerPenalties::dispatch($employer)
            ->delay(now()->addMinutes(2));

            return redirect()->back()->with('success', 'Dissolving scheduled in 2 mins');
        }catch (\Exception $e) {
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }
    
    /**
     * Builds up penalty of a particular loan
     *
     * @param  mixed $request
     * @param  mixed $loan
     * @param  mixed $penaltyService
     * @return void
     */
    public function buildupPenalty(Request $request, Loan $loan, BuildPenaltyService $buildPenaltyService)
    {
        try {
            
            DB::beginTransaction();

            $buildPenaltyService->build($loan);

            DB::commit();

            return redirect()->back()->with('success', 'All Build ups are complete');
        }catch (\Exception $e) {
           
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }



     /**
     * Builds up penalty on an employer
     *
     * @param  mixed $request
     * @param  mixed $employer
     * @return void
     */
    public function buildupPenaltyEmployer(Request $request, Employer $employer)
    {
        try {
            
            BuildEmployerPenalties::dispatch($employer)
            ->delay(now()->addMinutes(2));

            return redirect()->back()->with('success', 'All Build ups are scheduled for 2 mins');
        }catch (\Exception $e) {
           
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }




     /**
     * Cancel An Entry
     *
     * @param  mixed $request
     * @param  mixed $penaltyService
     * @return void
     */
    public function saveEntry(Request $request, PenaltyService $penaltyService)
    {
        try {
            
            DB::beginTransaction();
           
            $loan = Loan::whereReference($request->loan_ref)->first();
          
            $amount = $request->amount;

            $desc = $request->desc;

            $direction = $request->direction;

            $trnxDate = $request->date;

            $penaltyService->setCollectionDate($trnxDate);

            $direction == 2 ? $penaltyService->debitPenaltyCollection($loan, $amount, $desc) : 
                $penaltyService->creditPenaltyCollection($loan, $amount, $desc);


            $penaltyService->checkAndWithdrawPenalty($loan);

            DB::commit();

            return response()->json('Success');

        }catch(\Exception $e) {

            DB::rollback();
            return response()->json($e->getMessage(), 422);
        }

    }
}
