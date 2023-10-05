<?php
namespace App\Services;

use App\Models\Loan;
use App\Models\PenaltySweep;
use App\Notifications\Users\Penalty\LoanPenalizedNotification;
use App\Notifications\Users\Penalty\LoanPenaltyBuildUpNotification;
use App\Notifications\Users\Penalty\LoanPenaltyWarningNotification;
use App\Notifications\Users\Penalty\LoanPenaltyCardSweepNotification;

class PenaltyNotificationService
{
    
    /**
     * Notify that loan has been personalized
     *
     * @param  mixed $loan
     * @return void
     */
    public function notifyLoanPenalized(Loan $loan)
    {
        $user = $loan->user;

        $user->notify(new LoanPenalizedNotification($loan));
    }
    
    /**
     * Warn user on pending penalty
     *
     * @param  mixed $loan
     * @return void
     */
    public function warnOnPenalty(Loan $loan)
    {
        $user = $loan->user;

        $user->notify(new LoanPenaltyWarningNotification($loan));
    }
    
    /**
     * Notify a user that penalty will builtup on 
     *
     * @param  mixed $loan
     * @return void
     */
    public function  notifyPenaltyBuildup(Loan $loan)
    {
        $user = $loan->user;
        $user->notify(new LoanPenaltyBuildUpNotification($loan));
    }
    

    /**
     * Notify user that card was sweept
     *
     * @param  mixed $loan
     * @return void
     */
    public function notifyCardSweep(PenaltySweep $sweep)
    {
        $loan = $sweep->loan;
        $user = $loan->user;

        $user->notify(new LoanPenaltyCardSweepNotification($loan, $sweep));
    }



}