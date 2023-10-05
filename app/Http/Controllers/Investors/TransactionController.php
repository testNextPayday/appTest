<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\PDF;
use App\Models\WalletTransaction;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:investor');
    }
    
    public function index()
    {
        $from = request('from');
        $to = request('to');
       
        $investor = auth()->guard('investor')->user();
         if($from && $to){
            
            $transactions = $investor->transactions()->whereBetween('created_at',[$from,$to])->latest()->get();
            return view('investors.transactions.wallet', compact('transactions', 'investor','from','to'));
             
         }else{
             $transactions = $investor->transactions()->latest()->get();
         }
       
        return view('investors.transactions.wallet', compact('transactions', 'investor','from','to'));
    }
    public function showInflow(){
        $today = \Carbon\Carbon::today();
         if (request('month')) {
            $month = request('month');
        } else {
            $month = $today->month;    
        }
        
        if (request('year')) {
            $year = request('year');
        } else {
            $year = $today->year;    
        }
         $investor = auth()->guard('investor')->user();
        
        $repayments = $investor->repayments->map(function($repayment){
           return $repayment->plan; 
        });
        if($month  && $year){
          $transactions = $repayments->where('status','1');  
        }else{
        $transactions = $repayments->where('status','1')->where('date_paid','<=',$month);
        }
        
        return view('investors.transactions.inflow',compact('transactions', 'investor','month','year'));
    }
    
    public function emailTransaction(){
        
        $from = request('from');
        $to = request('to');
        
        $investor = auth()->guard('investor')->user();
         if($from && $to){
            
            $transactions = $investor->transactions()->whereBetween('created_at',[$from,$to])->latest()->paginate(20);
            $pdf = PDF::loadView('pdf.invoice', $transactions);
            
         }else{
             $transactions = $investor->transactions()->latest()->get();
             $pdf = PDF::loadView('pdfs.investment_statement', $transactions);
             return $pdf->download('invoice.pdf');
            //  Mail::to($investor)->send(new InvestmentStatement($transactions));
         }
    }
    
}
