<?php
namespace App\Services\Penalty;


class PenaltySettingRetrieve

{
    protected $loan;


    public function __construct($loan) 
    {
        $this->loan = $loan;

    }

     /**
     * getSettingsFor
     *
     * @param  mixed $loan
     * @return void
     */
    public function getSettings()
    {
        
        if($setting = $this->loan->penaltySetting) {
          
            return $setting;
        } 
       
        $employment = ($this->loan->loanRequest)->employment;

        if ($employment) {

            if ($setting = $employment->employer->penaltySetting){

                return $setting;
            }
        }

        return false;
    }
}