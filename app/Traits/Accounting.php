<?php

namespace App\Traits;

use App\Models\Settings;

trait Accounting
{

    
    /**
     * This is the PHP version of the PMT function used in Excel
     *  
     * @param float $rate
     * @param int $nper
     * @param float $pv
     * @param float $fv
     * @param int $type
     * @return float
     */
    public function getEMI($rate = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0)
    {
        if ($rate > 0) {
            return (-$fv - $pv * pow(1 + $rate, $nper)) / (1 + $rate * $type) / ((pow(1 + $rate, $nper) - 1) / $rate);
        } else {
            return (-$pv - $fv) / $nper;
        }
    }

    /**
     * Calculating flat emi
     * 
     */
    public function getPrincipal($amount, $tenure)
    {
        return round(($amount / $tenure), 2);
    }

    public function getInterest($rate, $amount, $tenure)
    {
        $a = ($rate * $tenure) * (12 / $tenure);
        $interest = $a / 12 * $amount;
        return round($interest, 2);
    }

    public function getFlatEmi($rate, $amount, $tenure)
    {
        return $this->getInterest($rate, $amount, $tenure) + $this->getPrincipal($amount, $tenure);
    }


   



    // This functions works like the simple loan calculator in excel
    

    public function pmt($amount, $rate,$tenure)
    {
       
        $rate = round(($rate)/100,5);
        $a = $amount * $rate;
        $b = 1 -  pow((1 + $rate),- $tenure);
       return $a/$b;
    }

   
}
