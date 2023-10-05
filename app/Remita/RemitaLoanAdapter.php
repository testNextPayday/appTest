<?php 
namespace App\Remita;

use App\Models\LoanMandate;
use App\Models\Loan;
use App\Helpers\Constants;
use App\Remita\DDM\MandateManager;
use App\Unicredit\Logs\DatabaseLogger;

class RemitaLoanAdapter
{
    // This class is written to abstract the activities carried out on loan
    // after mandate manager has been used


    /**
     * Setups stop mandate
     *
     * @param  mixed $loan
     * @return void
     */
    public function stopMandate(Loan $loan)
    {
        $mandateManager = new MandateManager();

        $dbLogger = new DatabaseLogger();
       

        $response = $mandateManager->stopMandate($loan);

        $dbLogger->log($loan,$response,'mandate-stop');

        if ($response->isASuccess()) {
            // mandate is incactive 
            $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 1);
        }

        session()->flash('info', 'Mandate Stopage Sent');
        
        return $response;
    }

    
    /**
     * Setup Mandate 
     *
     * @param  mixed $loan
     * @return void
     */
    public function setupMandate(Loan $loan, $dates = [])
    {
        $mandateManager = new MandateManager();

        $dbLogger = new DatabaseLogger();

         // Setup mandate
         $response = !empty($dates) ? $mandateManager->setupMandate($loan, $dates[0], $dates[1]) : $mandateManager->setupMandate($loan);
        
         //Log Response;
         $dbLogger->log($loan, $response);

         //Log Mandate 
         $data = [
             'loan_id'=> $loan->id,
             'status'=> $response->isASuccess(),
             'mandateId'=> $response->mandateId
         ];

         LoanMandate::create($data);
         
         //evaluates response and takes necessary action
        
         // if response indicates success, sets the loan status as awaiting activation
         if ($response->isASuccess()) {
             
             $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 1);
             
             $loan->update([
                 'mandateId' => $response->getMandateId(),
                 'requestId' => $response->getRequestId(),
             ]);
         }

         return $response;
    }
}