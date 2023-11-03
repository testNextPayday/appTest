<?php
namespace App\Traits;

use App\Models\BankDetail;
use Illuminate\Support\Facades\Http;

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

    public function banks(){
        $getBanks =  Http::get('https://api.paystack.co/bank');
        $banks = json_decode($getBanks);

        return $banks->data;
    }
}
?>