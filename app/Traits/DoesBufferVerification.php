<?php
namespace App\Traits;

use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;

trait DoesBufferVerification
{


    public function startVerification()
    {
        $planIds = [];

        $service = $this->service;

         // STEP 1 : Get unverified buffers and verify them
        PaymentBuffer::verifiable()->chunk(50, function($buffers) use($service, &$planIds){
            foreach ($buffers as $buffer) {

                if (!$buffer->transaction_ref) continue;
                
                // This code block can throw an error but we want to continue if that happens
                try {
                    array_push($planIds, $buffer->plan_id);
                    $service->verifyBuffer($buffer);
    
                }catch(\Exception $e) {
                    continue;
                }
                
            }
        });


        // STEP 2 : Verify Plans based on new buffer status
        $planIds  = array_unique($planIds);

        RepaymentPlan::whereIn('id', $planIds)->chunk(50, function($plans) use($service){
            foreach ($plans as $plan ) {
                // if plan is paid skipp
                if ($plan->status == true) continue;
             
                // This code can throw an error if that happens we continue
                try {
    
                    $service->verifyRepaymentPlanOnBuffers($plan);
    
                }catch(\Exception $e) {
    
                    continue;
                }
                
            }
        });
    }
}