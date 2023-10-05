<?php
namespace App\Unicredit\Contracts;

interface IPromissoryPaymentService
{
    
    /**
     * Pays interest on a particular promissory note
     *
     * @return void
     */
    public function payInterest();
}