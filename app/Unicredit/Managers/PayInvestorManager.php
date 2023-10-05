<?php
namespace App\Unicredit\Managers;

use Exception;
use App\Models\Investor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Unicredit\Collection\RepaymentManager;

class PayInvestorManager

{

    protected $mode;

    protected $repayer;

    protected $investors;

    protected $plans;

    public function __construct()
    {
        
        $this->repayer = new RepaymentManager();
        
    }

   

    public function issueRepaymentProcess($mode = 'backend')
    {
        $this->mode = $mode;
        $this->setInvestors();
      
        $this->getPlans();
       
        return $this->makePaymentForPlans();
       
    }

    protected function setInvestors()
    {
        $this->investors = Investor::where('payback_cycle', $this->mode)->get();
    }


    public function getInvestors()
    {
        return $this->investors;
    }


    public function getPlans()
    {  
        $this->plans  = $this->investors->map(function($investor, $index){
            return $investor->repaymentPlanCollection();
        })->collapse();
    }


    protected function makePaymentForPlans()
    {
       
        foreach ($this->plans as $plan) {
           
            try {
                DB::beginTransaction();

                $loan = $plan->loan;
                
                $this->repayer->settleInvestors($loan, $plan);
                
                $plan->update(['paid_out' => true]); 
               
                DB::commit();
                
            } catch (Exception $e) {
                DB::rollback();

                Log::debug($e->getMessage());
               
            }
            
        }
    }

    


    
}
?>