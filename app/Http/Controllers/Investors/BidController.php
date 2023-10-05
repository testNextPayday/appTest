<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Bid;
use App\Models\LoanFund;
use App\Models\WalletTransaction;

use App\Helpers\FinanceHandler;
use DB;
use Carbon\Carbon;

class BidController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:investor');
    }
    
    public function index()
    {
        $investor = auth()->guard('investor')->user();
        $bids = $investor->bids()->latest()->paginate(20);
        return view('investors.bids.index', compact('bids', 'investor'));
    }
    
    public function store(LoanFund $loanFund, FinanceHandler $financeHandler)
    {
        $investor = auth()->guard('investor')->user();
        $amount = request('amount');
        $seller = $loanFund->investor;
        $reference = $loanFund->reference;
        
        $data = [
            'investor_id' => $investor->id,
            'amount' => $amount,
            'fund_id' => $loanFund->id
        ];
        
        if ($bid = Bid::create($data)) {
    
            // TODO: send notifications
            
            $code = config('unicredit.flow')['asset_bid'];
            $financeHandler->handleDouble(
                $investor, $investor, $amount, $loanFund, 'WTE', $code
            );
            
            return response()->json([
                'status' => 1,
                'message' => 'Bid placed successfully',
                'bid' => $bid
            ], 200);
        }
        
        return response()->json([
            'status' => 0,
            'message' => "Bid could not be placed at this time. Please try again later"
        ], 400);
    }
    
    public function accept(Bid $bid, FinanceHandler $financeHandler)
    {
        DB::beginTransaction();
        
        try {
            $fund = $bid->loanFund;
            $bid->update(['status' => 2]);
            
            $otherBids = $fund->bids()->where('id', '!=', $bid->id)->whereStatus(1)->get();
            
            foreach($otherBids as $otherBid) {
                $this->cancelBid($otherBid, $fund, $financeHandler);
            }
            
            
            $fund->update(['status' => 5, 'transfer_date' => Carbon::now()]);
            
            $code = config('unicredit.flow')['asset_bid_approval'];
            $financeHandler->handleDouble(
                $bid->investor, $fund->investor, $bid->amount, $fund, 'ETW', $code
            );
            
            // create a new asset for the buyer
            $this->createChildAsset($fund, $bid);
            $fund->update(['sale_amount' => $bid->amount]);
            
            DB::commit();
            return response()->json(['status' => 1, 'message' => 'Bid accepted successfully'], 200);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 0, 'message' => $e->getMessage(), 'errors' => $e->errors()
            ], 400);
        }
    }
    
    public function reject(Bid $bid, FinanceHandler $financeHandler)
    {
        if ($bid->update(['status' => 3])) {
            $investor = $bid->investor;
            $fund = $bid->loanFund;
            $code = config('unicredit.flow')['asset_bid_rvsl'];
            $financeHandler->handleDouble(
                $investor, $investor, $bid->amount, $fund, 'ETW', $code
            );   
            return response()->json([
                'status' => 1, 'message' => 'Bid cancelled successfully', 'bid' => $bid
            ], 200);
        }
        
        return response()->json([
            'status' => 0, 
            'message' => 'Bid cannot be cancelled at this time. Please try again later'
        ], 400);
    }
    
    public function cancel(Bid $bid, FinanceHandler $financeHandler)
    {
        if ($this->cancelBid($bid, $bid->fund, $financeHandler)) {
            return response()->json([
                'status' => 1, 'message' => 'Bid cancelled successfully', 'bid' => $bid
            ], 200);
        } else {
            return response()->json([
                'status' => 0, 
                'message' => 'Bid cannot be cancelled at this time. Please try again later'
            ], 400);
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
        
        return LoanFund::create($data);
    }

}
