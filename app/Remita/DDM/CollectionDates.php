<?php
namespace App\Remita\DDM;

use Carbon\Carbon;


class CollectionDates
{


    public static function getStartDateHtml()
    {
        return static::getStartDate()->format('Y-m-d');
    } 


    public static function getEndDateHtml($loan)
    {
        return static::getEndDate($loan)->format('Y-m-d');
    } 

     /**
     * Determines collection start date for a loan
     * @return String date
     */
    public static function getStartDate()
    {
        $now = Carbon::today();
        
        $startDate = Carbon::today();

        // start date is always 25th
        $startDate->day = 25;
        
        // // if today is > 18, start month is next month
        if ($now->day > 18) {
            $startDate->addMonth();
        }
        
        return $startDate;
    }
    
    
    /**
     * Determines collection end date for a loan
     * @param Loan $loan
     * @return String date
     */
    public static function getEndDate($loan)
    {
        if (! isset($loan->duration)) {
            throw new \Exception("Cannot set end date of this loan with no duration");
        }
        $startDate = Carbon::createFromFormat('d/m/Y', static::getStartDate()->format('d/m/Y'));
        return $startDate->addMonths($loan->duration + 2);
    }
}