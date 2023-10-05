<?php 
namespace App\Services;

use App\Models\Loan;
use App\Helpers\Constants;
use App\Services\TopupService;
use App\Helpers\FinanceHandler;
use App\Traits\SettleAffiliates;
use App\Events\LoanDisbursedEvent;
use App\Events\InvestorsUpfrontInterestEvent;
use App\Helpers\TransactionLogger;
use App\Remita\DDM\MandateManager;
use Illuminate\Support\Facades\DB;
use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Support\Facades\Notification;
use App\Unicredit\Collection\RepaymentManager;
use App\Services\Penalty\PenaltyTakeOutService;
use App\Unicredit\Collection\CollectionService;
use App\Notifications\Users\LoanDisbursementNotification;
use App\Notifications\Investors\FundsDisbursedNotification;
use App\Notifications\Investors\UpfrontInterestNotification;


class LoanDisburseService 

{
    use SettleAffiliates;

    protected $collectionService;

    protected $topupService;

    /**
     * Disbursement Collection Service
     *
     * @param  \App\Unicredit\Collection\CollectionService $collectionService
     * @return void
     */
    public function __construct(CollectionService $collectionService)
    {

        $this->collectionService = $collectionService;

        $this->topupService = new TopupService();
    }

    
    /**
     * Disburses a loan from the backend
     *
     * @param  \App\Models\Loan $loan
     * @param  \App\Helpers\FinanceHandler $financeHandler
     * @return void
     */
    public function disburseFromBackend(Loan $loan)
    {

        if($loan->disbursalAmount() < 0) {
            throw new \Exception('Cannot disburse loan with negative disbursal amount');
        }
       
        $financeHandler  = new FinanceHandler(new TransactionLogger);

        // set up repayments
        //$this->collectionService->setUpRepayments($loan);
        $this->collectionService->setupArmotizedRepayments($loan);
        //update user escrow 
        //insurance = 2.5% of principal
        $insurance = 0.025 * $loan->amount;
        $user = $loan->user;
        
        // Mark loan as disbursed and active
        $loan->update(
            [
            'disburse_status' => 4,
            'status' => "1",
            'disbursal_amount' => $loan->disbursalAmount(),
            'active_at'=>now()->toDateTimeString()
            ]
        );
        
        if ($loan->is_top_up) {

            $this->topupService->handleTopUp($loan);
            // Clear this balance because it has been factored in
            $loan->user->update(['loan_wallet'=>0.0]);
        }
        // Check if theres an affiliate and credit only if this is the users first loan request
        $this->settleAffiliateOnLoan($user, $loan, $financeHandler);

        //$investors = $loan->loanRequest->investors();
        
        //Notification::send($investors, new FundsDisbursedNotification($loan));

        // Notify User
        $user = $loan->user;
        //$user->notify(new LoanDisbursementNotification($loan));

        $loanRequest = $loan->loanRequest;

        //check if the laon is upfront interest enabled
        if($loanRequest->upfront_interest){
            // pay investors that funded an upfront interest enabled loan
            event(new InvestorsUpfrontInterestEvent($loan));

            //Notify Investor
            //Notification::send($investors, new UpfrontInterestNotification($loan));
        }
    }

    public function disburseLoan(Loan $loan){

            if($loan->disbursalAmount() < 0) {
                throw new \Exception('Cannot disburse loan with negative disbursal amount');
            }            
            // set up repayments
            //$this->collectionService->setUpRepayments($loan);
            $this->collectionService->setupArmotizedRepayments($loan);
            //update user escrow 
            //insurance = 2.5% of principal
            $insurance = 0.025 * $loan->amount;
            $user = $loan->user;
            // Mark loan as disbursed and active
            $loan->update(
                [
                'disburse_status' => 4,
                'status' => "1",
                'disbursal_amount' => $loan->disbursalAmount(),
                'active_at'=>now()->toDateTimeString()
                ]
            );
           
            if ($loan->is_top_up) {

                $this->topupService->handleTopUp($loan);
                // Clear this balance because it has been factored in
                $loan->user->update(['loan_wallet'=>0.0]);
            }
            $financeHandler  = new FinanceHandler(new TransactionLogger);
            
            // Check if theres an affiliate and credit only if this is the users first loan request
            $this->settleAffiliateOnLoan($user, $loan, $financeHandler);          

            //$investors = $loan->loanRequest->investors();
            //Notification::send($investors, new FundsDisbursedNotification($loan));
            // Notify User
            $user = $loan->user;
            //$user->notify(new LoanDisbursementNotification($loan));

            // fire the loan disbursed event 
            // DO NOT TOUCH
            // DO NOT TOUCH
            event(new LoanDisbursedEvent($loan));
            
            /*$loanRequest = $loan->loanRequest;
            check if the laon is upfront interest enabled
            if($loanRequest->upfront_interest){
                 pay investors that funded an upfront interest enabled loan
                event(new InvestorsUpfrontInterestEvent($loan));

                Notify Investor
                Notification::send($investors, new UpfrontInterestNotification($loan));
            }*/
            
           
    }
    
    
  

}