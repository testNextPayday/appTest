<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\LoanFund;
use App\Models\WalletTransaction;

use App\Notifications\Users\LoanFundedNotification;

use App\Helpers\FinanceHandler;

use DB;

class LoanFundController extends Controller
{
    private $code;
    
    public function __construct()
    {
        $this->middleware('auth:investor');    
        $this->code = config('unicredit.flow')['loan_fund'];
    }
    
    /**
     * Display a listing of the investor funds.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $investor = auth()->guard('investor')->user();
        
        $funds = $investor->loanFunds()->latest()->paginate(10);

        return view('investors.loan-funds.index', compact('funds'));
    }
    
    public function acquired()
    {
        $investor = auth()->guard('investor')->user();
        
        $funds = $investor->acquiredFunds()->latest()->paginate(10);

        return view('investors.loan-funds.acquired', compact('funds'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Funds a loan request.
     *
     * @param App\Models\LoanRequest
     * @return \Illuminate\Http\Response
     */
    public function store(LoanRequest $loanRequest, FinanceHandler $financeHandler)
    {
        $investor = auth()->guard('investor')->user();
        
        $amount = $loanRequest->amount * request('percentage') / 100;
        
        if (!$investor->canPlaceInvestment($amount)) {
            return response()->json([
                'status' => 0,
                'message' => 'You do not have enough funds to invest this amount'
            ], 400);
        }
        
        $data = [
            'investor_id' => $investor->id,
            'request_id' => $loanRequest->id,
            'percentage' => request('percentage'),
            'amount' => $amount
        ];
        
        try {
            
            DB::beginTransaction();
            
            $loanFund = LoanFund::create($data);
            
            $borrower = $loanRequest->user;
            
            // $financeHandler->handleDouble(
            //     $investor, $borrower, $amount, $loanRequest, 'WTE', $this->code
            // );
            // No more pushing to borrowers escrow

            $financeHandler->handleSingle(
                $investor, 'debit', $amount, $loanRequest, 'W', $this->code
            );
            
            // reduce percentage left for loan request
            
            // TODO: send notifications
            $percentageLeft = $loanRequest->percentage_left - request('percentage');
            
            $update = ['percentage_left' => $percentageLeft];

            
            // Set loan request as accepted if fully funded
            if ($percentageLeft <= 0) {
                $update['status'] = 4;
            }
            
            $loanRequest->update($update);

            //$investor->notify(new LoanFundedNotification($loanFund));
            //$borrower->notify(new LoanFundedNotification($loanFund));

         
            DB::commit();
            
            // $this->sendNotifications($investor, $borrower, $loanFund);
            
            return response()->json(['status' => 1], 200);
            
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 400);
        }
    }
    
    /**
     * Shows a single loan fund.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(LoanFund $loanFund)
    {   
        $fundRepayment = $loanFund->where('id',$loanFund->id)
                                      ->with('repayment','loanRequest')->first();


        return view('investors.loan-funds.show', [
            'fund' => $loanFund, 
            'request' => $loanFund->loanRequest, 
            'repayment' => $loanFund->repayment,
            'fundRepayment' => $fundRepayment
        ]);
    }
    
    public function transfer(LoanFund $loanFund)
    {
        $this->validate(request(), ['amount' => 'required']);
        
        $data = [
            'status' => 4,
            'sale_amount' => request('amount')
        ];
        
        if ($loanFund->update($data)) {
            return response()->json([
                'status' => 1,
                'message' => 'Fund successfully placed on the market',
                'fund' => $loanFund->fresh()
            ], 200);
        }
        
        return response()->json([
            'status' => 0,
            'message' => 'Fund cannot be placed on the market at this time. Please try again later'
        ], 400);
    }
    
    public function market()
    {
        $funds = LoanFund::onSale()
                    ->where('investor_id', '!=', auth()->id())
                    ->latest()
                    ->paginate(20);
        return view('investors.loan-funds.market', compact('funds'));
    }
    
    private function sendNotifications($investor, $borrower, $loanFund)
    {
        //$borrower->notify(new LoanFundedNotification($loanFund));
        //$investor->notify(new LoanFundedNotification($loanFund));
    }
}
