<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Traits\EncryptDecrypt;
use App\Models\Loan;
use App\Models\User;
use Auth;

class LoanController extends Controller
{
    use EncryptDecrypt;

    public function getLoan(Loan $loan)
    {

        return response()->json($loan);
    }
    
    public function view($reference)
    {
        $loan = Loan::whereReference($reference)->first();
        $users = User::get(['name','id', 'reference']);

        

        if(!$loan || ($loan->user_id != Auth::id()))
            return redirect()->back()->with('failure', 'Loan does not exist');
        return view('users.loans.view', compact('loan', 'users'));
    }
    
    public function receivedLoans()
    {
        $loans = Auth::user()->receivedLoans;
        return view('users.loans.received', compact('loans'));
    }
}
