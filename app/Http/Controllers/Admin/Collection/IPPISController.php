<?php

namespace App\Http\Controllers\Admin\Collection;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Loan;
use App\Helpers\Constants;

use App\Traits\Managers\LoanManager;

class IPPISController extends Controller
{
    
    use LoanManager;
    
    
    /**
     * Approves an IPPIS loan by setting the value of the associated collection
     * Method to 2
     * 
     * @param Loan $loan The loan to be updated
     * @return Illuminate\Http\Response
     */
    public function approve(Loan $loan)
    {
        if ($loan->updateCollectionMethodStatus(Constants::DAS_IPPIS, 2)){
            return redirect()->back()
                        ->with('success', 'IPPIS Approved successfully');
        }
        
        return redirect()->back()
                    ->with('failure', 'IPPIS could not be approved');
    }
    
    
    /**
     * Declines an IPPIS loan by setting the value of the associated collection
     * Method to 4
     * 
     * @param Loan $loan The loan to be updated
     * @return Illuminate\Http\Response
     */
    public function decline(Loan $loan)
    {
        if ($loan->updateCollectionMethodStatus(Constants::DAS_IPPIS, 4)){
            return redirect()->back()
                        ->with('success', 'IPPIS declined successfully');
        }
        
        return redirect()->back()
                    ->with('failure', 'IPPIS could not be declined');
        
    }
}
