<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\Loan;
use App\Helpers\Constants;
use App\Remita\DDM\MandateManager;
use App\Services\LoanRepaymentService;
use App\Unicredit\Logs\DatabaseLogger;
use App\Services\Penalty\PenaltyService;
use App\Unicredit\Collection\RepaymentManager;
use App\Services\LoanRequestUpgradeService;

class TopupService
{

    protected $repaymentService;

    protected $upgradeService;

    public function __construct()
    {
        $this->repaymentService = new LoanRepaymentService();
        $this->upgradeService = new LoanRequestUpgradeService();
    }
      /**
     * Handles situations where topup is nvolved in a loan
     *
     * @param  \App\Models\Loan $loan
     * @return void
     */
    public function handleTopup(Loan $loan)
    {
       
        $referencedLoan = $loan->loanReference;

        if ($this->hasAMandate($referencedLoan)) {
            $this->attemptMandateStoppage($referencedLoan);
        }

        //(new RepaymentManager())->payOffInvestorOnTopup($referencedLoan);

        $this->repaymentService->makeTopupCollection($loan);

        if ($referencedLoan->is_penalized) {

            (new PenaltyService())->takeOutPenalty($referencedLoan);
        }

        if (!$referencedLoan->is_penalized) {

            // Upgrade User  
            //$maturity_time = Carbon::parse($loan->due_date);
            // $current_time = Carbon::now();
            // if($current_time->lte($maturity_time)) {                
            //    $this->upgradeService->upgradeUser($loan);
            // } 
            
            // if($this->upgradeService->checkLoanRequiresUpgrade($referencedLoan)){
            //     $this->upgradeService->upgradeUser($referencedLoan);
            // }
        }

        $repayments = $referencedLoan->repaymentPlans->where('status', 0);

        foreach ($repayments as $repayment) {
            $repayment->update(['status' => "1", 'collection_mode' => 'Set-off', 'date_paid' => now(),'paid_out'=>true,'wallet_balance'=>0.00]);
        }

        $referencedLoan->update(['status' => "2"]);

    }

    
    /**
     * Checks for mandate on a loan
     *
     * @param  mixed $loan
     * @return void
     */
    protected function hasAMandate($loan) 
    {
        return isset($loan->mandateId);
    }

    
    /**
     * Attempts to stop mandate on a loan
     *
     * @param  mixed $loan
     * @return void
     */
    protected function attemptMandateStoppage($loan)
    {
        $mandateManager = new MandateManager();

        $dbLogger = new DatabaseLogger();

        $response = $mandateManager->stopMandate($loan);

        $dbLogger->log($loan, $response, 'mandate-stop');

        if ($response->isASuccess()) {
            // mandate is incactive 
            $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 1);
        }

        session()->flash('info', 'Referenced Loan Mandate Stopage Sent');
    }
}