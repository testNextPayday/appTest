<?php

namespace App\Http\Controllers\Admin;

use App\Models\Log;
use App\Models\Loan;

use App\Helpers\Constants;

use Illuminate\Http\Request;
use App\Remita\DDM\MandateManager;
use App\Http\Controllers\Controller;
use App\Remita\DDM\MandateActivator;
use App\Traits\Managers\LoanManager;
use App\Unicredit\Logs\DatabaseLogger;
use App\Remita\AuthParams;
 

class MandateController extends Controller
{
    use LoanManager;
    
    
    /**
     * Approves a DAS loan by setting the value of the associated collection
     * Method to 2
     * 
     * @param Loan $loan The loan to be updated
     * @return Illuminate\Http\Response
     */
    public function approve(Loan $loan, $type = 'ippis')
    {
        $update = $type === 'ippis' ? $loan->updateCollectionMethodStatus(Constants::DAS_IPPIS, 2)
                    : $loan->updateCollectionMethodStatus(Constants::DAS_RVSG, 2);
        
        if ($update) {
            return redirect()->back()
                        ->with('success', 'Mandate Approved successfully');
        }
        
        return redirect()->back()
                    ->with('failure', 'Mandate could not be approved');
    }
    
    
    /**
     * Declines a DAS loan by setting the value of the associated collection
     * Method to 4
     * 
     * @param Loan $loan The loan to be updated
     * @return Illuminate\Http\Response
     */
    public function decline(Loan $loan, $type = 'ippis')
    {
        $update = $type === 'ippis' ? $loan->updateCollectionMethodStatus(Constants::DAS_IPPIS, 4)
                    : $loan->updateCollectionMethodStatus(Constants::DAS_RVSG, 4);

        if ($update) {
            return redirect()->back()
                        ->with('success', 'Mandate declined successfully');
        }
        
        return redirect()->back()
                    ->with('failure', 'Mandate could not be declined');
        
    }

    public function requestMandateOtp(Request $request,Loan $loan)
    {
        if (!isset($loan->mandateId)){

            return response()->json('No Mandate Found on loan', 422);
        }
        $response = (new MandateActivator())->requestAuthorization($loan);
       
        $dbLogger = (new DatabaseLogger())->log($loan,$response,'mandate-authorization');

        $mandate = $loan->mandates->last();
        if ($mandate) {
            $mandate->update(['remitaTransRef'=> $response->transactionRef]);
        }

        if($response->isASuccess()){
            return response()->json($response->getMessage(), 200);
        }
       
        $msg = $response->getMessage();
        return response()->json($msg, 422);
        
    }


    public function validateMandateOtp(Request $request,Loan $loan)
    {
        
        try{
         
            $transactionRef = @$loan->mandates->last()->remitaTransRef;
           
            $environment = app()->environment();

            // test urls requires this
            if ($environment == 'local' || $environment == 'staging')
            {
                $request->request->add(['card'=>'7890','otp'=>'1234']);
            }

            $first = new AuthParams();
            $first->param1 = "OTP";
            $first->value = $request->otp;

            $second = new AuthParams();
            $second->param2 = "CARD";
            $second->value = $request->card;

            $authParams = array($first,$second);
            
            $response = (new MandateActivator())->activateMandate($loan, $authParams, $transactionRef);

            $dbLogger = (new DatabaseLogger())->log($loan, $response,'mandate-activation');

            //TODO : Fix 502 proxy error
            if ($response->isASuccess()) {
                // mandate is active and loan is ready for disbursement
                $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 2);

                return response()->json($response->getMessage() ?? 'Validation was successful', 200);
            }
        
            return response()->json($response->getMessage() ?? 'Validation failed', 422);

        }catch(\Exception $e){

            return response()->json($e->getMessage());
        }
        
    }



    public function stopMandate(Request $request,Loan $loan)
    {
        try{

            $response = (new MandateManager())->stopMandate($loan);
            $dbLogger = (new DatabaseLogger())->log($loan,$response,'mandate-stop');
            if ($response->isASuccess()) {
                // mandate is incactive 
                $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 1);

                return redirect()->back()->with(['success'=>$response->getMessage()]);
            }
            // just in case it wasnt a success and no error
            return redirect()->back()->with(['success'=>'Mandate Instruction Stoppage sent']);
            
        }catch(\Exception $e){

            return redirect()->back()->with(['failure'=>$response->getMessage() ?? $e->getMessage()]);
        }
        
    }

    
    /**
     * History Mandate
     *
     * @param  mixed $request
     * @param  mixed $loan
     * @return void
     */
    public function historyMandate(Request $request, Loan $loan)
    {
        try{

            $response = (new MandateManager())->getMandateHistory($loan);
            //$dbLogger = (new DatabaseLogger())->log($loan,$response,'mandate-stop');
            return view('admin.mandate-history', ['response'=> $response]);
            
        }catch(\Exception $e){

            return redirect()->back()->with(['failure'=>$response->getMessage() ?? $e->getMessage()]);
        }
    }



    public function getRemitaBanks()

    {
        $data = array_keys(json_decode(json_encode(config('remita.banks_with_otp_support')),true));
        
        return response()->json($data,200);
    }



}
