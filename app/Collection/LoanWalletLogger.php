<?php
namespace App\Collection;

use App\Helpers\TransactionLogger;
use App\Models\LoanWalletTransaction;


class LoanWalletLogger 
{

  
    

    public function log($data)
    {
        LoanWalletTransaction::create($data);
    }
}