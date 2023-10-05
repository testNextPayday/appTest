<?php

namespace App\Http\Controllers;

use App\Models\LoanWalletTransaction;
use Illuminate\Http\Request;

class TotalPenaltiesController extends Controller
{
    //
    public static function total($loan)
    {
        return LoanWalletTransaction::where('loan_id', $loan->id)->where('is_penalty', 1)->sum('amount');
    }
}
