<?php

namespace App\Http\Controllers\Admin\Collection;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionInsightFormatter;
use App\Services\CollectionInsight\SuccessRateService;

class CollectionInsightDataController extends Controller
{
    //


    public function successRates(Request $request, SuccessRateService $successRateService)
    {
        
        $month = $request->month;
        $year  = $request->year;
        $employerID = $request->employerID;

        $chartPerformance =  $successRateService->collectionPerformanceFor($month, $year, $employerID);

        $overallCollection = $successRateService->overallPerformanceFor($month, $year, $employerID);

        return response()->json(['performance'=> $chartPerformance, 'overall'=>$overallCollection]);
    }

    public function collectionIntervals(Request $request, SuccessRateService $successRateService)
    {
        $interval = $request->interval;
        $employerID = $request->employerID;
        
        $data = $successRateService->performanceAnalysis($interval, $employerID);

        return response()->json($data);
    }


    
}
