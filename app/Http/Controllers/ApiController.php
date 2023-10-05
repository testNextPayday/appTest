<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\Investor;
use Illuminate\Http\Request;
use App\Http\Resources\InvestorStatisticResource;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\PenaltySetting;
use App\Models\User;
use Carbon\Carbon;

class ApiController extends Controller
{
    //

    public function getInvestor(Request $request)
    {
        $investor = Investor::find($request->id);
        $data = new InvestorStatisticResource($investor);
        return response()->json($data, 200);
    }



    public function jsonData(Request $request)
    {

        $data = Employer::primary()->get(['id', 'name']);
        return response()->json($data);
    }

    public function loanRecova($bvn)
    {

        $user = User::where('bvn', $bvn)->first();

        // dd($user->id);









        $loans = Loan::with('user', 'repaymentPlans')->where('user_id', $user->id)
            ->where('status', '1')
            // ->where('is_penalized', 0)
            ->get();



        foreach ($loans as $loan) {
            $excessBalance = TotalPenaltiesController::total($loan);

            $excesspenalties = [0];
            $lastP = abs($loan->user->masked_loan_wallet);

            // echo $loan->count() . '';

            // return;




            // if(!$loan)
            //     $loan =  Loan::withTrashed()->wherereference($reference)->first();
            /////////////////////////////Penalty that has passed loan duration ///////////////////////////////////////////////////////////////////



            if ($loan->is_penalized == 1) {

                $durationAfterExpiration = Carbon::parse($loan->created_at)->diffInMonths(now()) - $loan->duration;
                $loan_request = LoanRequest::where('id', $loan->request_id)->latest()->first();
                $penalty = PenaltySetting::where('entity_id', $loan_request->employment->employer->id)
                    ->first();
                $loanPenalty = PenaltySetting::where('entity_id', $loan->id);
                if ($loanPenalty->exists()) {
                    $penalty = $loanPenalty->latest()->first();
                    $penaltyPercent = $loanPenalty->latest()->first()->value / 100;
                } else {
                    $penaltyPercent = $penalty->value / 100;
                }
                if ($penalty->excess_penalty_status == 1) {
                    while ($durationAfterExpiration > 0) {
                        $currentPenalty = $lastP + ($lastP * $penaltyPercent);
                        $lastP = $currentPenalty;
                        $durationAfterExpiration--;
                        array_push($excesspenalties, $lastP);
                    }
                }
            }
            $lastPenalty = end($excesspenalties);

            if ($lastPenalty > 0) {
                $maturity_penalty = '-' . $lastPenalty;
            } else {
                $maturity_penalty = $loan->user->masked_loan_wallet;
            }
        }

        // dd($maturity_penalty);






        //////////////////////////////////////////////////////////////////////////////
        

        // if (!$loan)
        //     return redirect()->back()->with('failure', 'Loan does not exist');



        return response()->json([
            
            'active_loan' => $loan,
            'previous_loans' => Loan::where('user_id', $user->id)->where('status', '!=', 1)->get(),
            'excessBalance' => $excessBalance,
            'excesspenalties' => $excesspenalties,
            'maturity_penalty' => $maturity_penalty,

        ]);


        
        // return view('admin.loans.show', compact('loan', 'excessBalance', 'excesspenalties', 'maturity_penalty'));
    }
}