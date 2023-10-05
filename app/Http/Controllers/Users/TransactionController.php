<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index()
    {
        $user = auth()->guard('web')->user();
        $transactions = $user->transactions()->latest()->paginate(20);
        return view('users.transactions.wallet', compact('transactions', 'user'));
    }
}
