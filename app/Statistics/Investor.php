<?php
namespace App\Statistics;
use App\Models\Investor as ModelInvestor;
use App\Models\LoanFund;
use App\Models\WalletTransaction;
use App\Models\Repayment;
use App\Models\WithdrawalRequest;

class Investor 
{

    public function __construct($investor,$start_date,$end_date)
    {
        $this->investor = isset($investor) ? ModelInvestor::find($investor) : null;
        $this->from = $start_date;
        $this->to = $end_date;
    }

    public function wallet_fund()
    {
        $info = '';
        if($this->investor) {
            $funds = $this->investor->transactionQuery()
                ->whereBetween('created_at',[$this->from,$this->to])
                ->where('code',000)
                ->with('owner')
                ->get(['id','amount','description','reference']);
        }else{
            $funds = WalletTransaction::whereBetween('created_at',[$this->from,$this->to])
                ->where('code',000)
                ->with('owner')
                ->get(['id','amount','description','reference']);
        }
        $info .= " Total Amount  ₦" . number_format($funds->sum('amount'), 2);
        $info .= " Total Number of Activities " . $funds->count();

        return [$info, $funds];
            
    }

    public function loan_funds()
    {
        $info = '';
        if($this->investor){
            $funds = $this->investor->loanFundsQuery()
            ->whereBetween('created_at',[$this->from,$this->to])
            ->get(['id','amount','percentage']);
        }else{
            $funds = LoanFund::whereBetween('created_at',[$this->from,$this->to])
           
            ->get(['id','amount','percentage']);
        }
        $info .= " Total Amount  ₦" . number_format($funds->sum('amount'), 2);
        $info .= " Total Number of Activities " . $funds->count();

        return [$info, $funds];
    }



    public function recoveries()
    {
        $info = '';
        if($this->investor){
            $funds = $this->investor->repaymentsQuery()
            ->whereBetween('created_at',[$this->from,$this->to])
            ->where('reversed',0)
            ->get(['id','amount']);
        }else{
            $funds = Repayment::whereBetween('created_at',[$this->from,$this->to])
            ->where('reversed',0)
            ->get(['id','amount']);
        }
        $info .= " Total Amount  ₦" . number_format($funds->sum('amount'), 2);
        $info .= " Total Number of Activities " . $funds->count();

        return [$info, $funds];
    }


    public function withdrawals()
    {
        $info = '';
        if($this->investor){
            $funds = $this->investor->withdrawalsQuery()
            ->whereBetween('created_at',[$this->from,$this->to])
            ->where('status',2)
            ->get(['id','amount']);
        }else{
            $funds = WithdrawalRequest::whereBetween('created_at',[$this->from,$this->to])
            ->where('status',2)
            ->get(['id','amount']);
        }
        $info .= " Total Amount  ₦" . number_format($funds->sum('amount'), 2);
        $info .= " Total Number of Activities " . $funds->count();

        return [$info, $funds];
    }
}

?>