<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Bid;
use App\Models\LoanRequest;
use App\Models\WalletTransaction;
use App\Models\Loan;

use Carbon\Carbon;

use Auth, DB, Validator;

class BidController extends Controller
{
    public function index()
    {
        $bids = Bid::where('entity', 1)->get();
        return view('admin.bids', compact('bids'));
    }
    
    public function requestBids($reference)
    {
        $loanRequest = LoanRequest::whereReference($reference)->first();
        $bids = $loanRequest->bids()->where('entity', 1)->whereUserId(Auth::id())->get();
        return $bids;
    }

}
