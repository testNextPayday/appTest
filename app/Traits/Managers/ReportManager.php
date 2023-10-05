<?php
namespace App\Traits\Managers;

trait ReportManager 
{


    public function getReportData($request)
    {
        switch($request->code)
        {
            case '001':
                return $this->getLoanDisbursementData($request);
                break;
            case '002':
                return $this->getFeesEarnedData($request);
                break;
            case '003':
                return $this->getCollectionsMade($request);
                break;
            case '004':
                return $this->getPenalties($request);
            case '005':
                return $this->getCommissionsGiven($request);
            default:

        }
    }

}
?>