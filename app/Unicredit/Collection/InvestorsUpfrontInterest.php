<?php
namespace App\Unicredit\Collection;

/**
 * UpfrontInterest.php 
 * author: @Esther
 * * 
 * Check for loan request upfront interest, handles investor upfront interest,
 *  commisions and tax payment and sends notifications
 */
use App\Models\Loan;
use App\Models\UpfrontInterest;
use App\Models\LoanFund;
use App\Helpers\TransactionLogger;
use App\Helpers\FinanceHandler;


class InvestorsUpfrontInterest
{
    private $financeHandler;

    private $dbLogger;


    public function __construct()
    {
        $this->financeHandler = new FinanceHandler(new TransactionLogger);        
    }

    public function settleInvestors(Loan $loan)
    {   $loanRequest = $loan->loanRequest;
        $funds = $loanRequest
            ->funds()
            ->where('is_current', true)
            ->whereIn('status', [2, 4])
            ->get();

        foreach($funds as $fund) {
            $this->settleInvestor($fund, $loan);
        }
    }


    private function settleInvestor(LoanFund $fund, Loan $loan)
    {
        $investor = $fund->investor;

        $loanFund = $fund;

        $loanRequest = $loan->loanRequest;

        $upfrontInterest = $loanRequest->investorUpfrontInterest;

        if($loanRequest->upfront_interest){            
            $interest = $upfrontInterest->interest;
            while ($loanFund->original_id) {
                $loanFund = $loanFund->original;
            }        
            $fundFraction = $loanFund->amount / $loan->amount;
    
            $investorsCut = $fundFraction * $interest;       
            $interestFraction = $fundFraction * $interest;
            $commission = $interestFraction * ($investor->commission_rate / 100);
            $tax = $interestFraction * ($investor->tax_rate / 100);
          
            //$investorsCutAfterCommission = $investorsCut - $commission;
            
            $this->handleFinance(
                $investor, $fund, $investorsCut, $commission, $tax
            );
        }

        
    }

    private function handleFinance($investor, $fund, $investorsCut, $commission, $tax){
        $code = config('unicredit.flow')['investor_upfront_interest'];
        $this->financeHandler->handleSingle(
            $investor,
            'credit',
            $investorsCut,
            $fund,
            'V',
            $code
        );

       
        // No more commission as we now do portfolio management
        // Commissions are back
        
        $code = config('unicredit.flow')['fund_recovery_fee'];
        $this->financeHandler->handleSingle(
            $investor,
            'debit',
            $commission,
            $fund,
            'V',
            $code
        );

         $code = config('unicredit.flow')['tax_payment'];
        $this->financeHandler->handleSingle(
            $investor,
            'debit',
            $tax,
            $fund,
            'V',
            $code
        );

        // here we simply update the investor last vault
        $investor->update(['last_vault_inflow'=> now()]);
    }

}