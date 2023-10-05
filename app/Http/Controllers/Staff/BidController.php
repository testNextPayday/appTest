<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Bid;
use App\Models\WalletTransaction;
use App\Traits\EncryptDecrypt;
use App\Helpers\FinanceHandler;

use App\Notifications\Users\BidCancelledNotification;

use Auth;

class BidController extends Controller
{
    use EncryptDecrypt;
    
    public function index()
    {
        $staff = Auth::guard('staff')->user();
        $bids = $staff->bids;
        return view('staff.bids', compact('staff', 'bids'));
    }
    
    public function cancel($bid_id, FinanceHandler $financeHandler)
    {
        $bid_id = $this->basicDecrypt($bid_id);
        if(!$bid_id) abort(404);
        $bid = Bid::find($bid_id);
        if(!$bid) return redirect()->back()->with('failure', 'Bid not found');
        $data = ['status' => 4];
        if($bid->update($data)) {
            //notify owner of cancelled bid
            $owner = $bid->loanFund->investor;
            $owner->notify(new BidCancelledNotification($bid));
            $this->cancelBid($bid, $bid->loanFund, $financeHandler);
            
            return redirect()->back()->with('success', 'Bid cancelled.');
        } else {
            return redirect()->back()->with('failure', 'An error occurred. Please try again');
        }
    }
    
    private function cancelBid($bid, $fund, FinanceHandler $financeHandler)
    {
        $bid->update(['status' => 4]);
                
        $investor = $bid->investor;
        $code = config('unicredit.flow')['asset_bid_rvsl'];
        
        $financeHandler->handleDouble(
            $investor, $investor, $bid->amount, $fund, 'ETW', $code
        );
        
        $investor->notify(new BidCancelledNotification($bid));
        
        return true;
    }
}
