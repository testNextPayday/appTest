<?php
namespace App\Traits;

use App\Services\Investor\BackendPromissoryService;
use App\Services\Investor\MonthlyPromissoryService;
use App\Services\Investor\UpfrontPromissoryService;


trait PromissoryPaymentMethod
{

    
    /**
     * Inject a service payment method based on the interest payment cycle
     *
     * @return void
     */
    public function getServiceAttribute()
    {
        $interestCycle = strtolower($this->interest_payment_cycle);

        switch($interestCycle) {

            case 'upfront': 
                return new UpfrontPromissoryService($this);
            break;

            case 'monthly':
                return new MonthlyPromissoryService($this);
            break;

            case 'backend':
            default:
                return new BackendPromissoryService($this);
        }
    }

}