<?php

namespace Tests\Unit\Traits;
use App\Models\Refund;
use App\Events\RefundApprovedEvent;
use Illuminate\Support\Facades\Event;

trait RefundTest
{
    
    
    public function canMakeRefund($route,$entity,$guard)
    {

        $user = factory(\App\Models\User::class)->create();

        Event::fake();
        
        $refund = factory(\App\Models\Refund::class)->make(['user_id'=>$user->id]);

        $data = $refund->toArray();

        $user->update(['loan_wallet'=> $data['amount']]);

        $staff = factory($entity)->create();

        $response = $this->actingAs($staff, $guard)->post($route, $data);
        
        $this->assertDatabaseHas(
            'refunds', [
            'user_id'=>$refund->user_id,
            'loan_id'=>$refund->loan_id,
            'amount'=>$refund->amount]
        );

       
        $refund = Refund::where('loan_id', $refund->loan_id)->first();

        if($entity === "App\Models\Admin")
        {
            Event::assertDispatched(RefundApprovedEvent::class);
          
            $this->assertTrue($refund->status == 1);

        }else{

            Event::assertNotDispatched(RefundApprovedEvent::class);
         
            $this->assertTrue($refund->status == 0);
        }

       
    }

}

?>