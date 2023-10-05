<?php

namespace App\Http\Controllers\Admin;

use App\Models\LoanFund;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoanFundController extends Controller
{
    //


    
    /**
     * Return view for loan funds
     *
     * @return void
     */
    public function index()
    {
        $funds = LoanFund::with(['loanRequest.loan.user', 'investor'])->get();
        return view('admin.loan-funds.index', ['funds'=> $funds]);
    }

   
}
