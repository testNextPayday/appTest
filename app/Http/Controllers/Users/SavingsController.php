<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Savings;
use App\Models\SavingsSettings;
use App\Models\SavingsTransaction;
use Carbon\Carbon;
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

        $user = Auth::user();
        if(Savings::where('user_id', $user->id)->where('status', 1)->count() > 0){
            return back()->with('failure', 'You can only have one active savings plan');
        }

        if($user->wallet < $this->settings()->minimum_savings){
            return back()->with('failure', 'You must have a minimum of '.$this->settings()->minimum_savings . ' in your wallet before creating a savings plan');
        }

        $r->validate([
            'name' => 'required',
            'target_amount' => 'required|integer|min:'.$this->settings()->minimum_savings,
            'payback_date' => 'required|date_format:Y-m-d',
        ]);

        $paybackDate = Carbon::parse($r->payback_date);
        // dd($paybackDate);

        // dd(now()->diffInMonths($paybackDate));
        $duration = (now()->diffInMonths($paybackDate) + 1);

        if ($duration < $this->settings()->minimum_duration) {
            return back()->with('failure', 'Minimum savings duration is '.$this->settings()->minimum_duration.' months');
        }

        $amount = $user->wallet;

        $savings = Savings::create([
            'user_id' => $user->id,
            'name' => $r->name, 
            'amount' => $user->wallet,
            'duration' => $duration,
            'target_amount' => $r->target_amount,
            'pay_back_date' => $r->payback_date,
        ]);

        $user->wallet = 0;
        $user->save();

        SavingsTransaction::create([
            'user_id' => $user->id,
            'savings_id' => $savings->id,
            'amount' => $amount,
            'description' => 'Savings funding',
            'type' => 'credit',
            'status' => 'success'
        ]);

        if ($savings) {
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