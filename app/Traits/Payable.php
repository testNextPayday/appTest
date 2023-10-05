<?php
namespace App\Traits;

use App\Models\BankDetail;

trait Payable 
{

    /** This class helps those payable administer their bank records */

    public function addBeneficiaryAccount(array $data)
    {

        return BankDetail::create([

            'owner_id'=>$this->id,

            'owner_type'=>get_class($this),
            
            'account_number'=> $data['account_number'],

            'bank_name'=> $data['bank_name'],

            'bank_code'=> $data['bank_code']
            
        ]);

    }
}
?>