<?php
namespace App\Services;

use App\Models\Employment;

class ExcelUserFinder
{

    protected $payrollID;

    protected $userName;

    protected $employerID;

    public function __construct($payrollID, $userName, $employerID)
    {
        $this->payrollID = $payrollID;
        $this->userName = $userName;
        $this->employerID = $employerID;
    }
    
    /**
     * findUser
     *
     * @return void
     */
    public function findUser()
    {
        // Search user by payroll id and specific employer id
        $possibleUsers = Employment::with('user')->whereHas('user')->where(['payroll_id'=> $this->payrollID, 'employer_id'=>$this->employerID])->get();

        $name = $this->userName;
        // Find user by the given name
        $possibleUsers = $possibleUsers->filter(function($emp)use($name) {return $emp->user->hasName($name);});
        if ($possibleUsers->count() <= 1) {
            return $possibleUsers->last();
        }
        
        // Find user with active loan
        $possibleUsers = $possibleUsers->filter(function($emp){ return $emp->user->activeLoans()->count() > 0;});
        if ($possibleUsers->count() <= 1) {
            return $possibleUsers->last();
        }

        // If i have gone through all this steps then i will simply just get the last user of the collection and return
        return $possibleUsers->last();

    }

}