<?php
namespace App\Services\CollectionInsight;

use Carbon\Carbon;
use App\Http\Resources\CollectionInsightFormatter;
use App\Services\CollectionInsight\CollectionManagerService;

class SuccessRateService
{

    protected $collectionInsight;

    public function __construct(CollectionManagerService $collectionInsight)
    {
        $this->collectionInsight = $collectionInsight;
    }

    
    /**
     * Calculate success rates
     *
     * @param  mixed $month
     * @param  mixed $year
     * @param  mixed $method
     * @param  mixed $employerID
     * @return void
     */
    public function collectionPerformanceFor($month, $year, $employerID)
    {
        $collections  = $this->collectionInsight->getCollectionsBy($month, $year, $employerID);

        return $collections;
    }

    
    /**
     * Overall Collection Performance
     *
     * @param  mixed $month
     * @param  mixed $year
     * @param  mixed $employerID
     * @return void
     */
    public function overallPerformanceFor($month, $year, $employerID)
    {
        $insighter = $this->collectionInsight;
        return ['Actual Collection'=>$insighter->getCollectionsMade($month, $year, $employerID), 'Expected Collection'=>$insighter->getExpectedCollections($month, $year, $employerID)];
    }

    
    /**
     * Retrieve Bare Collection
     *
     * @param  mixed $interval
     * @param  mixed $employerID
     * @return void
     */
    public function retrieveBareCollections($interval, $employerID)
    {
        $monthsNeeded = $this->getDatesBetweenInterval($interval);

        $actual = $this->generateCollectionsMade($monthsNeeded, $employerID);

        $expected = $this->generateCollectionsExpected($monthsNeeded, $employerID);

        return ['Actual Collections'=>$actual, 'Expected Collections'=> $expected];
    }
    
    /**
     * Performance Analysis
     *
     * @param  mixed $interval
     * @param  mixed $employerID
     * @return void
     */
    public function performanceAnalysis($interval, $employerID)
    {   
        
        $monthsNeeded = $this->getDatesBetweenInterval($interval);

        $actual = $this->generateCollectionsMade($monthsNeeded, $employerID);

        $expected = $this->generateCollectionsExpected($monthsNeeded, $employerID);

        return ['Actual Collections'=>$actual, 'Expected Collections'=> $expected];
    }

    
    /**
     * Generate Collections Made Over a duration
     *
     * @param  mixed $monthsNeeded
     * @param  mixed $employerID
     * @return void
     */
    public function generateCollectionsMade($monthsNeeded, $employerID)
    {
        $collections = [];
        $insighter = $this->collectionInsight;
        
        foreach($monthsNeeded as $index=>$month){
           
            $collections[$index] = $insighter->getCollectionsMade($month[0], $month[1], $employerID);
        }

        return $collections;
    }


    /**
     * Generate Collections Made Over a duration
     *
     * @param  mixed $monthsNeeded
     * @param  mixed $employerID
     * @return void
     */
    public function generateCollectionsExpected($monthsNeeded, $employerID)
    {
        $collections = [];
        $insighter = $this->collectionInsight;

        foreach($monthsNeeded as $index=>$month){

            $collections[$index] = $insighter->getExpectedCollections($month[0], $month[1], $employerID);
        }

        return $collections;
    }

    
    /**
     * I try to generate the months between this interval of time 
     *
     * @param  mixed $interval
     * @return void
     */
    public function getDatesBetweenInterval($interval) 
    {
        $now = Carbon::now();

        $reviewList = [];

        while($interval > 0 ) {
            $prev = $now->subMonth();
            $key = $prev->format('M Y');
            $monthYearPair = [$prev->month, $prev->year];
            $reviewList[$key] =  $monthYearPair;
            $interval--;
        }

        asort($reviewList);

        return $reviewList;
    }


}