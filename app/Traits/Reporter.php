<?php
namespace App\Traits;

use App\Models\Loan;

class Reporter 
{

    public function amountDisbursed($from,$to)
    {
        if(is_null($from) || is_null($to)){
            return Loan::whereStatus("1")->get()->sum('disbursal_amount');
        }
        
    }

}
?>