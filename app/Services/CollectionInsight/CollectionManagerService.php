<?php
namespace App\Services\CollectionInsight;

use App\Helpers\Constants;
use App\Models\RepaymentPlan;
use App\Http\Resources\CollectionInsightFormatter;


class CollectionManagerService
{
    // The cases of this indexes are cas sensitive to data to previously exists in the database 
    // Changing it will definitely break the code .. Please beware
    protected $codeMap = [
        'PAYSTACK'=> '300',
        'REMITA'=> ['100', '200'],
        'DDAS'=> '201',
        'OKRA'=>'101',
        'RVSG'=> '202',
        'Default'=> [0, '400'],
    ];
    /**
     * Retrieve collections
     *
     * @param  mixed $month
     * @param  mixed $year
     * @param  mixed $employerID
     * @return void
     */
    public function getCollectionsBy($month, $year, $employerID)
    {
        $paystackCollections  = $this->baseCollectionsFor($month, $year, 'PAYSTACK', $employerID);
        $remitaCollections = $this->baseCollectionsFor($month, $year, 'REMITA', $employerID);
        $ddasCollections = $this->baseCollectionsFor($month, $year, 'DDAS', $employerID);
        $rvsgCollections = $this->baseCollectionsFor($month, $year, 'RVSG', $employerID);
        $defaultCollections = $this->baseCollectionsFor($month, $year, ['Cash', 'check', 'Transfer', 'Deposit', null, 'Set-off', 'Cheque'], $employerID);
        
        $collectionList =  [
            'Paystack'=> round($paystackCollections->sum('emi'), 2),
            'Remita'=> round($remitaCollections->sum('emi'), 2),
            'DDAS'=> round($ddasCollections->sum('emi'), 2),
            'RVSG'=> round($rvsgCollections->sum('emi'), 2),
            'Default'=> round($defaultCollections->sum('emi'), 2),
        ];

        return $this->nonEmptyCollections($collectionList);
    }

    
    /**
     * getBareCollectionsMade
     *
     * @param  mixed $month
     * @param  mixed $year
     * @param  mixed $employerID
     * @return void
     */
    public function getBareCollectionsMade($month, $year, $employerID)
    {
        $plans  = RepaymentPlan::whereYear('date_paid', $year)->whereMonth('date_paid', $month)->where('status', 1)->with(['loan.loanRequest.employment', 'buffers']);

        $plans = $plans->get();

        if (json_decode($employerID)) {
            $plans = $plans->filter(function($plan) use($employerID) {
                $employment = @optional($plan->loan->loanRequest)->employment;
                if ($employment) { 
                    return $employment->employer_id == $employerID;
                }
                return false;
            });
        }

        return $plans;
    }

    
    /**
     * Collections Made for a particular Month
     *
     * @param  mixed $month
     * @param  mixed $year
     * @param  mixed $employerID
     * @return void
     */
    public function getCollectionsMade($month, $year, $employerID)
    {
        $plans = $this->getBareCollectionsMade($month, $year, $employerID);
        return round($plans->sum('emi'), 2);
    }


    
    /**
     * getExpectedBareCollections
     *
     * @param  mixed $month
     * @param  mixed $year
     * @param  mixed $employerID
     * @return void
     */
    public function getExpectedBareCollections($month, $year, $employerID)
    {
        $plans  = RepaymentPlan::whereYear('payday', $year)->whereMonth('payday', $month)->with(['loan.loanRequest.employment', 'buffers']);

        $plans = $plans->get();

        if (json_decode($employerID)) {
            $plans = $plans->filter(function($plan) use($employerID) {
                $employment = @optional($plan->loan->loanRequest)->employment;
                if ($employment) { 
                    return $employment->employer_id == $employerID;
                }
                return false;
            });
        }

        return $plans;
    }

    
    
    /**
     * Expected collections for the month
     *
     * @param  mixed $month
     * @param  mixed $year
     * @param  mixed $employerID
     * @return void
     */
    public function getExpectedCollections($month, $year, $employerID)
    {
        $plans = $this->getExpectedBareCollections($month, $year, $employerID);
        return round($plans->sum('emi'), 2);
    }
    
    
    
   


   /**
    * Base collections for utilized by each collection method
    *
    * @return void
    */
   protected function baseCollectionsFor($month, $year, $collectionMethod, $employerID) 
   {

        $plans  = RepaymentPlan::whereYear('date_paid', $year)->whereMonth('date_paid', $month)->with(['loan.loanRequest.employment', 'buffers']);

        $plans = $plans->get();
        
        $plans = $plans->filter(function($plan) use($collectionMethod){
           
            return is_array($collectionMethod) ? in_array($plan->collection_mode, $collectionMethod) : $plan->collection_mode == $collectionMethod;
           
        });

        if (json_decode($employerID)) { // decoding because every thing here is stringified
          
            $plans = $plans->filter(function($plan) use($employerID, $collectionMethod) {
                
                $plan->expectedCollectionMethod = $collectionMethod; // We pass the collection method we will check against
                $employment = @optional($plan->loan->loanRequest)->employment;
                if ($employment) { 
                    return $employment->employer_id == $employerID;
                }
                return false;
            });
        }
        return $plans;
        //return CollectionInsightFormatter::collection($plans);
   }

   
   /**
    * Resolve the loan booking 
    *
    * @param  mixed $method
    * @return void
    */
   protected function resolveCollectionCode($method) 
   {
       if (is_array($method)) {
           return $this->codeMap['Default'];
       }
       return $this->codeMap[$method];
   }

   
   /**
    * Take off empty collections from this array
    *
    * @param  mixed $collections
    * @return void
    */
   protected function nonEmptyCollections(&$collections) 
   {
        
        return $collections;
   }



}