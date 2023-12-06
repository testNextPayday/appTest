<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Savings;
use App\Models\SavingsSettings;
use App\Models\SavingsTransaction;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavingsController extends Controller
{
    //

    public function index()
    {
        $savings =  Savings::where('user_id', Auth::id())
                    // ->where
                    ->where('status', 1)->get();
        return view('users.savings.index', compact('savings'));
    }

    public function settings(){
        return SavingsSettings::latest()->first();
    }

    public function store(Request $r)
    {
        // if(Savings::where('user_id', Auth::id())->where('status', 1)->count() > 0){
        //     return back()->with('failure', 'You can only have one active savings plan');
        // }

        // dd($this->settings()->minimum_savings);
        $newdate = now()->addMonths($this->settings()->minimum_duration);

        // dd($newdate);
        // dd(now()->diffInMonths($newdate));
        $min_duration = now()->diffInMonths($newdate);

        

        $r->validate([
            'name' => 'required',
            'target_amount' => 'required|integer|min:'.$this->settings()->minimum_savings,
        'payback_date' => 'required|date_format:Y-m-d|:'.now()->diffInMonths($newdate),
            // 'target_duration' => 'required|date_format:Y-m-d|after:today',
        ]);

        $paybackDate = $r->payback_date;

        dd($paybackDate);

        // if (condition) {
        //     # code...
        // }




        $store = Savings::create([
            'user_id' => Auth::id(),
            'name' => $r->name,
            'target_amount' => $r->target_amount,
            'target_duration' => $r->target_duration,
        ]);

        

        if ($store) {
            return back()->with('success', 'Savings plan created successfully');
        } else {
            return back()->with('failure', 'An error occurred. Please try again');
        }
    }

    public function view($id)
    {
        $savings = Savings::where('user_id', Auth::id())->findOrFail( $id)->first();
        $savings_transactions = SavingsTransaction::where('savings_id', $id)->get();

        return view('users.savings.view', compact('savings', 'savings_transactions'));
      
    }
}