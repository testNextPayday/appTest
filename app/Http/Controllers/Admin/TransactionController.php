<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\WalletTransaction;

class TransactionController extends Controller
{
    public function index()
    {
      
        $transactions = WalletTransaction::with('owner')->latest()->paginate(300);
        return view('admin.transactions.index', compact('transactions'));
    }
}