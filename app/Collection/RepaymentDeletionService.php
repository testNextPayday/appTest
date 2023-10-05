<?php
namespace App\Collection;

use App\Models\LoanWalletTransaction;


class RepaymentDeletionService
{

        
    /**
     * Handles bulk delete of a plan
     *
     * @param  mixed $data
     * @return void
     */
    public function handleBulkLogDeletion($data)
    {
        foreach($data as $upload) {
          
            $trnx = LoanWalletTransaction::find($upload);
        
            if ($trnx) {
                $trnx->delete();
            }
        
        }
    }
}