<?php
namespace App\Unicredit\Contracts;



interface LoanArmotizerStrategy 
{
    
    /**
     * This method starts the armotization process
     *
     * @param  \App\Structures\RestructuringObject $params
     * @return void
     */
    public function setupArmotizedRepayments($loan);
}
?>