<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use Illuminate\Http\Request;
use App\Traits\LoanSweepManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;

class LoanSweeperController extends Controller
{
    
        
    /**
     *  Get loans with card
     * 
     * @param  \Illumintae\Http\Request $request
     * @return void
     */
    public function loansWithCard(Request $request)
    {
        $loans = Loan::with('user')->where('collection_plan', 300)->get()->filter(function($loan){
            return $loan->isActive();
        });

        $loanCollection = LoanResource::collection($loans);

        return response()->json(['status'=>true, 'loans'=> $loanCollection]);
    }
}
