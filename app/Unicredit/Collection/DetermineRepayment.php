<?php
namespace App\Unicredit\Collection;


class DetermineRepayment 
{

    
    /**
     * investor
     *
     * @var \App\Models\Investor
     */
    protected $investor;
    
    /**
     * plan
     *
     * @var \App\Models\RepaymentPlan
     */
    protected $plan;

    
    /**
     * Initialise
     *
     * @param  mixed $investor
     * @param  mixed $plan
     * @return void
     */
    public function __construct($investor, $plan)
    {
        $this->investor = $investor;
        $this->plan = $plan;
    }

    
    
    /**
     * Get the current total 
     *
     * @return void
     */
    public function getCurrentTotal(){
        $plan = $this->plan;
        $interest = $plan->interest;
        $principal = $plan->principal;
        $mgt_fee  = $plan->management_fee;
        $total = 0;

        if ($this->investor->takes_mgt){
           $total = $interest + $principal + $mgt_fee;
        } else{
            $total = $interest + $principal;
        }
        return $total;
    }

    
    /**
     * Calculates total interest
     *
     * @return void
     */
    public function getTotalInterest(){
        $plan = $this->plan;
        $interest = $plan->interest;
        $mgt_fee  = $plan->management_fee;

        $t_interest = 0;

        if ($this->investor->takes_mgt) {
            $t_interest = $interest + $mgt_fee;

        }else {

            $t_interest = $interest;
        }

        return $t_interest;
    }
}