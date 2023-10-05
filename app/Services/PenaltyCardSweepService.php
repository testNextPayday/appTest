<?php
namespace App\Services;

use App\Models\Loan;
use App\Models\PenaltySweep;
use App\Paystack\PaystackService;
use App\Services\Penalty\PenaltyService;
use App\Services\PenaltyNotificationService;


class PenaltyCardSweepService
{

    protected $paystackService;

    public function __construct(PaystackService $service)
    {
        $this->paystackService = $service;
    }
    
    /**
     * Sweep on a loan by loan basis
     *
     * @param  \App\Models\Loan $loan
     * @return void
     */
    public function sweepLoan(Loan $loan)
    {
        if ($loan->user->loan_wallet > 0) { return false; }

        $debt = abs($loan->user->loan_wallet);

        $charge = round(1/8 * $debt, 2); // we sweep 1/8 for each penalty owed

        $user = $loan->user;
        
        $card = $user->billingCards->last();

        if (!$card) { return false;}
        
        $sweepStore = $this->createSweepStoreData($loan, $charge);

        // Push the users card
        $this->attemptCard($charge, $user, $card);

        // Verify card push
        $this->verifyCardAttempt();

        $this->logAttempt($sweepStore, $loan);
    
    }

    protected function createSweepStoreData($loan, $charge)
    {
        return PenaltySweep::create([
            'loan_id'=> $loan->id,
            'user_id'=> $loan->user->id,
            'amount'=> $charge
        ]);
    }

    
    
    /**
     * Log Attempt
     *
     * @param  mixed $sweepStore
     * @param  mixed $loan
     * @return void
     */
    protected function logAttempt($sweepStore, $loan)
    {

        $penaltyService = new PenaltyService();

        $verifyResponse = $this->paystackService->getResponse();

        $updates = [];

        $updates['dump'] = json_encode($verifyResponse);
        $updates['status_message'] = $verifyResponse['data']['status'];
        $updates['reference'] = $verifyResponse['data']['reference'];
        

        if ($this->chargeSuccess($verifyResponse)) {
            
           $updates['status'] = 1;

           $notification = new PenaltyNotificationService();

           $notification->notifyCardSweep($sweepStore, $loan);
           
           // Create a new penalty entry
           $penaltyService->creditPenaltyCollection($loan, $sweepStore->amount, $desc='Paystack Card Sweep');
        }

        $sweepStore->update($updates); 
    }
    
    /**
     * Charges a customers card for the penalty
     *
     * @param  double $charge
     * @param  \App\Models\User $user
     * @param  \App\Models\BillingCard $card
     * @return void
     */
    protected function attemptCard($charge, $user, $card) {
        $body = [
            'email' => $user->email,
            'amount' => $charge * 100, // change to Kobo
            'authorization_code' => $card->authorization_code,
        ];

        $chargeUrl = "/transaction/charge_authorization";
        
        $this->paystackService->setHttpResponse($chargeUrl, "POST", $body);

    }

    
    /**
     * Checks that a charge was successful
     *
     * @param  mixed $verifyResponse
     * @return void
     */
    protected function chargeSuccess($verifyResponse) 
    {
        return $verifyResponse['status'] === true && $verifyResponse['data']['status'] === 'success';
    }

    
    /**
     * Verify the card attempt
     *
     * @return void
     */
    protected function verifyCardAttempt(){

        $response = $this->paystackService->getResponse();

        $reference = $response['data']['reference'];

        $verifyUrl = "/transaction/verify/{$reference}";
        $this->paystackService->setHttpResponse($verifyUrl, "GET", $data=[]);
    }
    
}