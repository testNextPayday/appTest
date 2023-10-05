<?php
namespace App\Helpers;

use App\Models\Settings;


class InvestorAffiliateFees

{
    
    /**
     * Get Investor Rate For Investor Rate
     *
     * @param  mixed $loan
     * @return void
     */
    public static function getFullRate($loan)
    {
        $collector = $loan->collector_type == 'AppModelsUser' ? null : $loan->collector;

        $affiliateRate = 0;

        if ($collector ) {

            if ($collector instanceof \App\Models\User) {

                $affiliateRate = Settings::borrowerCommissionRate();
            }
            elseif ($collector instanceof \App\Models\Affiliate) {

                $affiliateRate = $collector->commission_rate;
            } else {
                $affiliateRate = 0;
            }
        }
        
        $supervisorRate = Settings::supervisorCommissionRate();

        $fullRate = $supervisorRate + $affiliateRate;

        return $fullRate;
    }
}