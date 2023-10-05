<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Settings;
use App\Helpers\Constants;
use GuzzleHttp\Client;
use App\Models\MonoPayment;

use App\Remita\DAS\LoanDisburser;
use App\Remita\RemitaLoanAdapter;

use App\Remita\DDM\MandateManager;
use App\Unicredit\Logs\DatabaseLogger;
use App\Services\MonoStatement\BaseMonoStatementService;


trait CollectionMethodHandlers
{
    /**
     * @var array
     * Maps collection codes to approriate handlers
     */
    private $collectionHandlers = [
        "100" => "remitaDDMHandler",
        "101"=>  "okraDDMHandler",
        "102"=> "monoDDMHandler",
        "200" => "remitaDASHandler",
        "201" => "ippisDASHandler",
        "202" => "rvsgDASHandler",
        "300" => "paystackCardHandler",
        "400"=>   "defaultDEFAULTHandler",
        "500"=> "wisetraderWISETRADERHandler"
    ];
    
    
    /**
     * Sets up a Remita DDM Loan
     * 
     * @param Loan $loan
     * @return array
     */
    public function remitaDDMHandler(Loan $loan)
    {
       
        $remitaLoanAdapter = new RemitaLoanAdapter();

        // check if a loan is being restructured or setup again
        if(isset($loan->mandateId)){

            $remitaLoanAdapter->stopMandate($loan);
        }
    
        $startDate = $this->startDate;
        $endDate = $this->endDate;
       
        $response = $this->hasDates ? $remitaLoanAdapter->setupMandate($loan, [$startDate, $endDate]) : $remitaLoanAdapter->setupMandate($loan);
        // else returns without taking an action, essentially leaving the loan as unattended
        
        return ['status' => $response->isASuccess()];
    }

    public function okraDDMHandler(Loan $loan)
    {
        return ['status' => true];
    }

    public function monoDDMHandler(Loan $loan)
    {
        $monoservice = new BaseMonoStatementService(new Client);
        //generate mono payment link
        $my_array = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $my_arrays = str_shuffle($my_array);
        $my_arrayz = str_shuffle($my_array);
        $reference = rand(0000,9000).substr($my_arrays,25).rand(0,9).rand(1000,9000).substr($my_arrayz,25);
        $amount = $loan->emi;

        //initiating mono payment
        $monoservice->initiatePayment($amount, $reference);
        $monoInfo = $monoservice->getResponse(); 
                 
        $paymentId = $monoInfo['id'];        
        $reference = $monoInfo['reference'];
        $paymentLink = $monoInfo['payment_link'];        
        
        $loan->update([
            'mono_payment_link'=>$paymentLink,
            'mono_payment_reference'=>$reference,
        ]);

        return ['status' => true];
    }
    
    
    /**
     *  Sets up a Remita DAS Loan
     * 
     *  @param Loan $loan
     *  @return array
     */
    public function remitaDASHandler(Loan $loan)
    {
        // Emi + Management Fee
        $monthlyPayment = $loan->emi + $loan->amount * (Settings::managementFee() /100);
        
        // Issue disbursement order
        $response = (new LoanDisburser())->disburse($loan, $monthlyPayment);
        
        //Log Response
        (new DatabaseLogger())->log($loan, $response);
        
        
        // If dusbursement is successful, 
        //Update Loan with Mandate Ref and change its status 
        if ($response->isASuccess() && $response->hasData()) {
            
            $loan->updateCollectionMethodStatus(Constants::DAS_REMITA, 2);
            
            $loan->update([
                'mandateId' => $response->getData()->mandateReference,
            ]);
        }
        
        return ['status' => $response->isASuccess()];
    }
    
    
    /**
     * Sets up an IPPIS DAS Loan
     * @param Loan $loan
     * @return array
     */
    public function ippisDASHandler(Loan $loan)
    {
        // Update, if exists, the IPPIS Collection method to status one
        // This keeps the loan in a state where the user needs to download a
        // disbursement authority. After they upload, admin disburses.
        return ['status' => $loan->updateCollectionMethodStatus(Constants::DAS_IPPIS, 2)];
    }
    
    
    /**
     * Sets up an RVSG DAS Loan
     * @param Loan $loan
     * @return array
     */
    public function rvsgDASHandler(Loan $loan)
    {
        // Update, if exists, the RVSG Collection method to status one
        // This keeps the loan in a state where the user needs to download a
        // disbursement authority. After they upload, admin disburses.
        return ['status' => $loan->updateCollectionMethodStatus(Constants::DAS_RVSG, 2)];
    }

    /**
     * Sets up an Default  Loan
     * @param Loan $loan
     * @return array
     */
    public function defaultDEFAULTHandler(Loan $loan)
    {
        // Update, if exists, the RVSG Collection method to status one
        // This keeps the loan in a state where the user needs to download a
        // disbursement authority. After they upload, admin disburses.
        return ['status' => $loan->updateCollectionMethodStatus(Constants::DEFAULT_DEFAULT, 2)];
    }

    /**
     * Sets up an WiseTrader  Loan
     * @param Loan $loan
     * @return array
     */
    public function wisetraderWISETRADERHandler(Loan $loan)
    {
        // Update, if exists, the RVSG Collection method to status one
        // This keeps the loan in a state where the user needs to download a
        // disbursement authority. After they upload, admin disburses.
        return ['status' => $loan->updateCollectionMethodStatus(Constants::WISETRADER_WISETRADER, 2)];
    }
    
    
    /**
     * Sets up an Paystack Card Loan
     * @param Loan $loan
     * @return array
     */
    public function paystackCardHandler(Loan $loan)
    {
        // Just update to 2 to allow admin disburse loans
        // TODO: Set up a proper card system

        // if the user has a card attached it his/her account continue
       
            
            return ['status' => $loan->updateCollectionMethodStatus(Constants::CARDS_PAYSTACK, 0)];
        
        
    }
}