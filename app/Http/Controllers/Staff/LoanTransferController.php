<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\LoanFund;
use App\Models\Bid;
use App\Models\Investor;

use App\Helpers\FinanceHandler;


use App\Notifications\Users\BidRejectedNotification;

use App\Notifications\Users\BidPlacedNotification;
use App\Notifications\Users\BidAcceptedNotification;
use App\Notifications\Users\BidUpdatedNotification;

use App\Traits\EncryptDecrypt;
use Carbon\Carbon;

use Auth, Validator, DB;

class LoanTransferController extends Controller
{
    use EncryptDecrypt;
    
    public function index()
    {
        $loans = LoanFund::whereStatus(4)
                    ->whereNull('staff_id')
                    ->orWhere('staff_id', '!=', Auth::id())
                    ->paginate(25);
        return view('staff.loans.transfers', compact('loans'));
    }
    
    public function placeBid(Request $request, FinanceHandler $financeHandler)
    {
        $rules = [
            'amount' => 'required',
            'investor_id' => 'required|min:1'
        ];
        
        $validator = Validator::make($request->all(), $rules);
        
        if($validator->fails()) {
            return response()->json(['status' => 0, 'message' => 'Please supply vaild amount and account'], 200);
        }
        
        $loan = LoanFund::find($request->loan_id);
        
        if(!$loan) 
            return response()->json(['status' => 0, 'message' => 'Loan not available'], 200);
            
        $investor = Investor::find($request->investor_id);
        if(!$investor) 
            return response()->json(['status' => 0, 'message' => 'Please provide a valid account'], 200);
            
        if($investor->wallet < $request->amount) 
            return response()->json(['status' => 0, 'message' => 'This account does not have enough money to make this bid'], 200);
        
        $data = [
            'investor_id' => $investor->id,
            'amount' => $request->amount,
            'fund_id' => $loan->id,
            'staff_id' => Auth::guard('staff')->user()->id,
            'staff_bid' => true
        ];
        
        if ($bid = Bid::create($data)) {
            $code = config('unicredit.flow')['asset_bid'];
            $financeHandler->handleDouble(
                $investor, $investor, $amount, $loanFund, 'WTE', $code
            );
            
            //sms and emails to bidder and request owner
            $investor->notify(new BidPlacedNotification($bid));
            $loan->investor->notify(new BidPlacedNotification($bid));
            
            return response()->json([
                'status' => 1,
                'message' => 'Bid placed successfully',
                'bid' => $bid
            ], 200);
        }
        
        return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);
    }
    
    public function updateBid(Request $request)
    {
        $validationRules = [
            'amount' => 'required',
        ];
        
        $validator = Validator::make($request->all(), $validationRules);
        if($validator->fails()) {
            return response()->json(['status' => 0, 'message' => 'Please supply an amount'], 200);
        }
        
        $bid = Bid::find($request->bid_id);
        
        //TODO: check if loan is still valid and on transfer
        if (!$bid) return response()->json(['status' => 0, 'message' => 'Bid not found'], 200);
        $loan = $bid->loanFund;
        if($loan->status != 4) {
            return response()->json(['status' => 0, 'message' => 'Loan transfer is no longer available'], 200);
        }
        
        $user = $bid->investor;
        
        $bidChange = $request->amount - $bid->amount;
        if($bidChange > 0 && $user->wallet < $bidChange) {
            return response()->json(['status' => 0, 'message' => 'You do not have enough money in your wallet to update this bid'], 200);
        }
        
        $walletData = [
            'wallet' => $user->wallet - $bidChange,
            'escrow' => $user->escrow + $bidChange
        ];
        
        $bidData = [
            'amount' => $request->amount,
        ];
        
        DB::beginTransaction();
        try {
            $bid->update($bidData);
            $user->update($walletData);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);  
        }
        DB::commit();
        $owner = $bid->loanFund->investor;
        $owner->notify(new BidUpdatedNotification($bid));
        $user->notify(new BidUpdatedNotification($bid));
        return response()->json(['status' => 1, 'message' => 'Counter offer placed successfully'], 200);
        
    }
    
    public function acceptBid($bid_id, FinanceHandler $financeHandler)
    {
        $bid_id = $this->basicDecrypt($bid_id);
        if(!$bid_id) abort(404);
        $bid = Bid::find($bid_id);
        if(!$bid) return redirect()->back()->with('failure', 'Bid not found');
        
        $data = ['status' => 2];
        if($bid->update($data)) {
            
            //do neccessary wallet updates
            $oldLoan = $bid->loanFund;
            $oldLoan->update(['status' => 5, 'transfer_date' => Carbon::now()]);
            
            $code = config('unicredit.flow')['asset_bid_approval'];
            $financeHandler->handleDouble(
                $bid->investor, $oldLoan->investor, $bid->amount, $oldLoan, 'ETW', $code
            );
            $this->createChildAsset($oldLoan, $bid);
            
            
            //Cancel other bids for this loan transfer
            $otherBids = $oldLoan->bids()->where('id', '!=', $bid->id)->get();
            foreach($otherBids as $otherBid) {
                $this->cancelBid($otherBid, $oldLoan, $financeHandler);
            }
            //send notifications
            $oldLoan->investor->notify(new BidAcceptedNotification($bid));
            $bid->investor->notify(new BidAcceptedNotification($bid));
            return redirect()->back()->with('success', 'Bid accepted.');
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
        
        $investor->notify(new BidRejectedNotification($bid));
        
        return true;
    }
    
    private function createChildAsset(LoanFund $fund, Bid $bid)
    {
        $data = [
            'investor_id' => $bid->investor_id,
            'request_id' => $fund->request_id,
            'percentage' => $fund->percentage,
            'amount' => $bid->amount,
            'original_id' => $fund->id,
            'status' => 2
        ];
        
        if($bid->staff_bid) {
            $loanData['staff_fund'] = true;
            $loanData['staff_id'] = $bid->staff_id;
        }
        
        return LoanFund::create($data);
    }
}
