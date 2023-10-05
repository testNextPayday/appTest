<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Loan;
use App\Remita\DDM\MandateActivator;
use App\Helpers\Constants;

class MandateActivationController extends Controller
{
    
    private $mandateActivator;
    
    public function __construct(MandateActivator $mandateActivator)
    {
        $this->middleware('auth');
        
        $this->mandateActivator = $mandateActivator;
    }
    
    public function requestAuthorization(Loan $loan)
    {
        $response = $this->mandateActivator->requestAuthorization($loan);
        
        if ($response->isASuccess()) {
            
            return response()->json([
                'authParams' => $response->getAuthParams(),
                'requestId' => $response->getRequestId(),
                'mandateId' => $response->getMandateId(),
                'remitaTransRef' => $response->getTransactionRef()    
            ], 200);
            
        }
        
        return response()->json(['message' => $response->getMessage()], 400);
    }
    
    
    public function activateMandate(Loan $loan)
    {
        $response = $this->mandateActivator->activate($loan);
        
        if ($response->isASuccess()) {
            
            // Loan mandate has been activated, approve
            $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 2);
            
            return response()->json(['message' => 'Activation successful'], 200);
        }
        
        return response()->json(['message' => "Activation failed"], 500);
    }
}
