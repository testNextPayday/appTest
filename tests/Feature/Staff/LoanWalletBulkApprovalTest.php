<?php

namespace Tests\Feature\Staff;

use Tests\TestCase;
use App\Models\LoanWalletTransaction;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use App\Events\LoanWalletTransactionApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanWalletBulkApprovalTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Staff can upload payment
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffUploadsGetsApproved()
    {
        $staff = factory(\App\Models\Staff::class)->create();

        // added repayment permisson
        $staff->update(['roles'=> 'manage_repayments']);

        // list of logged repayments
        $logs = factory(\App\Models\LoanWalletTransaction::class, 3)->create(['is_logged'=> true]);

        //loop through each log and set their loan to active
        foreach($logs as $log){
            $loan = $log->loan;
            $loan->update(['status'=> '1']);
        }

        $planIds = $logs->pluck('plan_id')->toArray();
       
        $url = route('staff.approve.all.repayments');
        $data = [ 'repayments'=> implode($planIds, ',')];
       
        $response = $this->actingAs($staff, 'staff')->post($url, $data);
       
        foreach($planIds as $index=>$id){
            
            $this->assertDatabaseHas('loan_wallet_transactions', [
                'plan_id'=> $id,
                'is_logged'=> 0
            ]);
        }

        // Verify that the plans are now mark as paid
        foreach($planIds as $index=>$id){
            $this->assertDatabaseHas('repayment_plans', [
                'id'=> $id,
                'status'=> 1
            ]);
        }

    }
}
