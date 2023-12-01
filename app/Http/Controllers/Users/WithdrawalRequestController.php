<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\WithdrawalRequest;
use App\Models\Settings;
use App\Helpers\FinanceHandler;
use App\Models\Loan;
use App\Notifications\Users\WithdrawalRequestPlacedNotification;
use App\Traits\EncryptDecrypt;
use Auth;

class WithdrawalRequestController extends Controller
{
    use EncryptDecrypt;
    
    public function index()
    {
        $user = Auth::guard('web')->user();
        
        $withdrawalRequests = $user->withdrawals()->latest()->paginate(20);
        
        return view('users.withdrawals.index', compact('withdrawalRequests'));
    }
    
    public function store(Request $request, FinanceHandler $financeHandler)
    {
        $validationRules = [
            'amount' => 'required'  
        ];

        $activeAloan =  Loan::where('user_id', Auth::id())->where('status', 1)->count();

        if($activeAloan > 0)
        return redirect()->back()->with('failure', 'You cannot place a withdrawal at this moment because you are on active loan');    
        

        
        $this->validate($request, $validationRules);

        
        $user = auth()->guard('web')->user();

        if ($user->banks->isEmpty()) {

            return redirect()->back()->with('failure', 'Update bank details before creating withdrawals');    
        }
        
        
        if ($user->wallet < request('amount')) {
            return redirect()->back()->with('failure', 'Insufficient Funds');    
        }
        
        $ledger_balance = Settings::whereSlug('ledger_balance')->first();
        if($ledger_balance) {
            $ledger_balance = (double) $ledger_balance->value;
        } else {
            $ledger_balance = 0;
        }
        
        if(($user->wallet - $request->amount) < $ledger_balance) {
            return redirect()->back()->with('failure', 'You cannot withdraw down to the last ' . $ledger_balance . ' in your wallet');
        
        }

        try {

            $data = [
                'amount' => request('amount'),
            ];
            
            if ($withdrawal = $user->withdrawals()->create($data)) {
                $code = config('unicredit.flow')['withdrawal'];
                $financeHandler->handleDouble(
                    $user, $user, request('amount'), $withdrawal, 'WTE', $code
                );
                $user->notify(new WithdrawalRequestPlacedNotification($withdrawal));
                return redirect()->back()->with('success', 'Request placed successfully');
            }
        }catch (\Exception $e) {
            return redirect()->back()->with('failure', 'Unknown error occur. Try again');
        }
        
       
    }
    
    public function delete(WithdrawalRequest $request_id)
    {
        try {

            $withdrawalRequest = $request_id;
            if(!$withdrawalRequest) return redirect()->back()->with('failure', 'Withdrawal Request not found');
            if($withdrawalRequest->delete()) {
                return redirect()->back()->with('success', 'Deleted Successfully');
            } else {
                return redirect()->back()->with('failure', 'An error occurred. Please try again');
            }
        }catch (\Exception $e) {
            return redirect()->back()->with('failure', 'An error occurred. Please try again');
        }
       
    }
}
