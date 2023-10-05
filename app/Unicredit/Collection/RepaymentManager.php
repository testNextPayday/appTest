<?php

/**
 * RepaymentManager.php 
 * author: @melas
 * 
 * Checks for loan repayments, Handles investor refunds and comissions payouts
 * and sends repayment notifications
 * 
 */

namespace App\Unicredit\Collection;

use DB;

use App\Models\Loan;

use App\Models\LoanFund;
use App\Models\Settings;

use App\Models\Affiliate;
use App\Models\Repayment;
use App\Models\RepaymentPlan;
use App\Helpers\FinanceHandler;

use App\Helpers\TransactionLogger;
use Illuminate\Support\Collection;
use App\Recipients\DynamicRecipient;
use App\Remita\DDM\CollectionChecker;
use App\Helpers\InvestorAffiliateFees;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Collection\DetermineRepayment;
use App\Notifications\Investors\LoanToUpNotification;
use App\Notifications\Shared\DebitConfirmationNotification;

class RepaymentManager
{


    private $financeHandler;

    private $dbLogger;


    public function __construct()
    {
        $this->financeHandler = new FinanceHandler(new TransactionLogger);
        $this->dbLogger = new DatabaseLogger();
    }


    public function checkRepayments(Collection $loans)
    {
        foreach ($loans as $loan) {
            $checker = new CollectionChecker();

            $response = $checker->check($loan);

            // TODO: Log Response
            $this->dbLogger->log($loan, $response);

            $history = $response->getHistory();

            $this->updateRepaymentPlans($loan, $history);
        }
    }


    private function updateRepaymentPlans(Loan $loan, $history = [])
    {

        $numPaid = $loan->repaymentPlans()->whereStatus(true)->count();

        $successCases = array_filter($history, function ($item) {
            return $item->paymentStatus === "SUCCESS";
        });

        $reversed = array_reverse($successCases);

        if ($numPaid >= count($successCases)) return;

        for ($i = $numPaid; $i < count($successCases); $i++) {
            $repayment = $reversed[$i];

            $nextPlan = $loan->repaymentPlans()->whereStatus(false)->first();

            if (!$nextPlan) return;

            try {

                DB::beginTransaction();

                $nextPlan->update(
                    ['status' => true, 'date_paid' => $repayment->paymentDate]
                );

                // Settle if repayment has not been settled previously
                // This is a corrective measure for an issue we had
                if (!$nextPlan->status) {
                    $this->settleInvestors($loan, $nextPlan);
                }

                // if repayments are complete, mark loan as fullfilled    
                if ($loan->repaymentPlans()->whereStatus(false)->count() < 1)
                    $loan->update(['status' => "2"]);

                DB::commit();
                $this->sendRepaymentNotifications($nextPlan);
            } catch (Exception $e) {

                DB::rollback();
            }
        }
    }


    public function settleInvestors(Loan $loan, RepaymentPlan $plan)
    {
       
        $loanRequest = $loan->loanRequest;

        $funds = $loanRequest
            ->funds()
            ->where('is_current', true)
            ->whereIn('status', [2, 4])
            ->get();

        foreach ($funds as $fund) {
            $this->settleInvestor($fund, $loan, $plan);
        }
    }


    private function settleInvestor(LoanFund $fund, Loan $loan, RepaymentPlan $plan)
    {
        $investor = $fund->investor;

        $loanFund = $fund;

        while ($loanFund->original_id) {
            $loanFund = $loanFund->original;
        }

        $determinePayment = new DetermineRepayment($investor, $plan);

        $fundFraction = $loanFund->amount / $loan->amount;

        $currentTotal = $determinePayment->getCurrentTotal();

        $investorsCut = $fundFraction * $currentTotal;

        $interestFraction = $fundFraction * $determinePayment->getTotalInterest();

        $commission = $interestFraction * ($investor->commission_rate / 100);
        $tax = $interestFraction * ($investor->tax_rate / 100);      
        //$investorsCutAfterCommission = $investorsCut - $commission;        
        $this->handleFinance(
            $investor, $fund, $investorsCut, $commission, $tax
        );

        //TODO Log Repayment
        return $this->createRepayment(
            $fund, $loan, $plan, $investor, $investorsCut, $commission, $tax
        );
    }


    private function sendRepaymentNotifications(RepaymentPlan $plan)
    {
        try {
            // Notify Admin and Borrower  
            $loan->user->notify(new DebitConfirmationNotification($plan));
            $adminEmail = config('unicredit.admin_email');

            if ($adminEmail) {
                $admin = new DynamicRecipient($adminEmail);
                $admin->notify(new DebitConfirmationNotification($plan));
            }
        } catch (\Exception $e) { }
    }

    

    /**
     * one-Off Investor Settlement
     * 
     *  - This settles the investor when settlement is made
     * @param  mixed $loan
     *
     * @return void
     */
    public function payOffInvestorOnSettlement(Loan $loan)
    {
        //TODO : Get Investors
        $loanRequest = $loan->loanRequest;
        $funds = $loanRequest
            ->funds()
            ->where('is_current', true)
            ->whereIn('status', [2, 4])
            ->get();

        foreach ($funds as $fund) {
            $this->onSettlementPayOffFund($fund, $loan);
        }
    }

    
    /**
     * Pays off each fund involves in a settlement loan
     *
     * @param  \App\Models\LoanFund $fund
     * @param  \App\Models\Loan $loan
     * @return void
     */
    public function onSettlementPayOffFund($fund, $loan)
    {
        $investor = $fund->investor;
        //$investor->notify(new LoanToUpNotification($loan));
        $loanFund = $fund;

        while ($loanFund->original_id) {
            $loanFund = $loanFund->original;
        }

        $fundFraction = $loanFund->amount / $loan->amount;

        $investorsCut = $fundFraction * $loan->settlement->investors_cut;

        if(!$investorsCut) $investorsCut = $fundFraction * $loan->calculateInvestorsCut();
        $interestFraction = $fundFraction * $loan->calculateAccruedInterest();
         $commission = $interestFraction * ($investor->commission_rate / 100);
        $tax = $interestFraction * ($investor->tax_rate / 100);

        $this->handleFinance($investor, $fund, $investorsCut, $commission, $tax);
        
        $plan = $loan->repaymentPlans->first();

        // push the loan fund to fulfillment
        $loanFund->update(['status'=> '6']);
        return $this->createRepayment(
            $fund, $loan, $plan, $investor, $investorsCut, $commission, $tax
        );
    }

    
    /**
     * Settles all investors involves when a topup is made. Settlement is made on the referenced Loan
     *
     * @param  mixed $loan
     * @return void
     */
    public function payOffInvestorOnTopup($loan)
    {
        $loanRequest = $loan->loanRequest;
        $funds = $loanRequest
            ->funds()
            ->where('is_current', true)
            ->whereIn('status', [2, 4])
            ->get();

        foreach ($funds as $fund) {
            $this->onTopupPayOffFund($fund, $loan);
        }
    }
    
    /**
     * Pays off each fund when a topup is being made
     *
     * @param  mixed $fund
     * @param  mixed $loan
     * @return void
     */
    public function onTopupPayOffFund($fund, $loan)
    {
        $investor = $fund->investor;
        $loanFund = $fund;
        while ($loanFund->original_id) {
            $loanFund = $loanFund->original;
        }

        $fundFraction = $loanFund->amount / $loan->amount;
        $amount = $loan->repaymentPlans->where('status', 0)->sum('principal');
        $investorsCut = $fundFraction * $amount;
        $interestFraction = $fundFraction * $loan->getTopUpInterest();
        $commission = $interestFraction * ($investor->commission_rate / 100);
        $tax = $interestFraction * ($investor->tax_rate / 100);

        //setting the first plan as the repayment plan can't be null na
        $plan = $loan->repaymentPlans->where('status', 0)->first();

        $this->handleFinance(
            $investor, $fund, $investorsCut, $commission, $tax
        );        
        // push the loan fund to fulfillment
        $loanFund->update(['status'=> '6']);
        //TODO Log Repayment
        return $this->createRepayment(
            $fund, $loan, $plan, $investor, $investorsCut, $commission, $tax
        );
    }


    private function handleFinance(
        $investor, $fund, $investorsCut, $commission, $tax
    )
    {

        $code = config('unicredit.flow')['fund_recovery'];
        $this->financeHandler->handleSingle(
            $investor,
            'credit',
            $investorsCut,
            $fund,
            'V',
            $code
        );

        $shareCode = config('unicredit.flow')['investor_share_affiliate_cost'];

        $loan  = $fund->loanRequest->loan;

        $fullRate = InvestorAffiliateFees::getFullRate($loan);

        $monthlyCharge = (($fullRate/100) * $fund->amount) / $loan->duration;

        

        // Reclaim affiliate spendings from here
        $this->financeHandler->handleSingle(
            $investor,
            'debit',
            $monthlyCharge,
            $fund,
            'V',
            $shareCode
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



    private function createRepayment(
        $fund, $loan, $plan, $investor, $investorsCut, $commission, $tax
    )
    {
        //we are no longer taking commissions
        
        Repayment::create(
            [
            'fund_id' => $fund->id,
            'loan_id' => $loan->id,
            'user_id' => $loan->user->id,
            'investor_id' => $investor->id,
            'plan_id' => $plan->id,
            'amount' => $investorsCut,
            'commission' => $commission,
            'tax' => $tax
            ]
        );
       
    }
}
