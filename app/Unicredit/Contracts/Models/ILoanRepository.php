<?php
namespace App\Unicredit\Contracts\Models;

interface ILoanRepository
{
    
    /**
     * Retrives active loans
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getActiveStatusLoans();

    
    /**
     * Retrieves fulfilled loans
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getFulfilledLoans();
}