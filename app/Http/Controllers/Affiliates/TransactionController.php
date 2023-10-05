<?php

namespace App\Http\Controllers\Affiliates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:affiliate');
    }
    
    public function index()
    {
        $affiliate = auth('affiliate')->user();
        $transactions = $affiliate->transactions()->latest()->paginate(20);
        return view('affiliates.transactions.wallet', compact('transactions', 'affiliate'));
    }
}
