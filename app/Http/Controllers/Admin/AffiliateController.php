<?php

namespace App\Http\Controllers\Admin;

use App\Models\Staff;
use App\Models\Meeting;

use App\Models\Affiliate;
use App\Models\LoanRequest;
use Illuminate\Http\Request;
use App\Helpers\FinanceHandler;
use App\Traits\SettleAffiliates;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Notifications\Affiliates\StatusChanged;
use App\Notifications\Affiliates\MeetingScheduled;

class AffiliateController extends Controller
{
    
    use SettleAffiliates;
    
    /**
     * Returns all affiliates in a json response
     *
     * @return void
     */
    public function getAllActive()
    {
        $affiliates = Affiliate::active()->get();
        return response()->json($affiliates);
    }

    public function index()
    {
        $affiliates = Affiliate::latest()->get();
        
        return view('admin.affiliates.index', compact('affiliates'));
    }
    
    public function show(Affiliate $affiliate)
    {
        $supervisors = Affiliate::active()->get();
        
        // Get all future meetings
        $meetings = Meeting::whereDate('when', '>=', now())
                                ->latest()
                                ->get();

        $employers = Employer::primary()->get();
        $merchantEmployers = Employer::merchant()->get();

        $mapped = $affiliate->mappedEmployer()->pluck('employer_id');
                                
        return view('admin.affiliates.show', compact('affiliate', 'supervisors', 'meetings', 'employers','merchantEmployers', 'mapped'));
    }
    
    
    /**
     * Updates an affiliate account
     * 
     */
    public function update(Request $request, Affiliate $affiliate)
    {
        $update = $affiliate->update($request->all());
        
        if ($update) {
            
            return redirect()->back()
                        ->with('success', 'Affiliate account updated Successfully');
        }
        
        return redirect()->back()
                        ->with('failure', 'Affiliate could not be updated. Try again later');
    }
    
    /**
     * Verifies an affiliate account
     * 
     */
    public function verify(Request $request, Affiliate $affiliate)
    {
        $this->validate($request, ['commission_rate' => 'required|numeric']);
        
        $update = $affiliate->update([
            'verified_at' => now(), 
            'commission_rate' => $request->commission_rate,
            'commission_rate_investor' => $request->commission_rate_investor,
            'status' => true,
            'supervisor_id' => $request->supervisor_id,
            'supervisor_type' => 'App\Models\Staff'
        ]);
        
        if ($update) {
            $when = now()->addMinutes(10);
            
            $affiliate->notify((new StatusChanged("verified"))->delay($when));
            
            return redirect()->back()
                        ->with('success', 'Affiliate Verified Successfully');
        }
        
        return redirect()->back()
                        ->with('failure', 'Affiliate could not be verified. Try again later');
    }
    
    /**
     * Toggles the status of an affiliate
     */
    public function toggleStatus(Affiliate $affiliate)
    {
        $action = $affiliate->status ? 'deactivated' : 'activated';
        
        $update = $affiliate->update(['status' => !$affiliate->status]);
        
        if ($update) {
            $when = now()->addMinutes(10);
            
            $affiliate->notify((new StatusChanged($action))->delay($when));
            
            return redirect()->back()
                            ->with('success', "Affiliate $action successfully");
        }
        
        return redirect()->back()
                            ->with('failure', "Affiliate could not be $action. Please try again");
    }
    
    /**
     * Sets the supervisor of an affiliate
     */
    public function setSupervisor(Request $request, Affiliate $affiliate)
    {
        $this->validate($request, ['supervisor_id' => 'required|numeric']);
        
        $update = $affiliate->update([
            'supervisor_id' => $request->supervisor_id,
            'supervisor_type' => 'App\Models\Affiliate'
        ]);
        
        if ($update) {
            return redirect()->back()
                        ->with('success', 'Supervisor assigned successfully');
        }
        
        return redirect()->back()
                        ->with('failure', 'Supervisor could not be assigned. Try again later');
    }
    
    /**
     * Sets up a meeting for affiliate
     */
    public function scheduleMeeting(Request $request, Affiliate $affiliate)
    {
        $this->validate($request, ['meeting_id' => 'required|numeric|exists:meetings,id']);
        
        $update = $affiliate->update(['meeting_id' => $request->meeting_id]);
        
        if ($update) {
            // Notify affiliate
            $when = now()->addMinutes(10);
            
            $affiliate->notify((new MeetingScheduled($affiliate->meeting))->delay($when));
            
            return redirect()->back()
                            ->with('success', 'Meeting scheduled successfully');
        }
        
        return redirect()->back()
                        ->with('failure', 'Meeting could not be scheduled');
    }


    public function settleAffiliatePage()
    {
        // unassigned loanRequests
        $loanRequests = LoanRequest::unassigned()->with('user')->get();
        $affiliates = Affiliate::active()->get();
        return view(
            'admin.affiliates.settle', 
            ['loanRequests'=>$loanRequests, 'affiliates'=>$affiliates]
        );
    }


    public function settleAffiliateCommission(Request $request, FinanceHandler $financeHandler)
    {
        try {
           
            DB::beginTransaction();

            $affiliate = Affiliate::find($request->affiliateId);

            $loanRequest = LoanRequest::find($request->loanRequestId);

            if (! $loan = $loanRequest->loan) {

                throw new \Exception('Cannot settle affiliate when loan has not been created');
            }

            if ( $loan->status != '1') {

                throw new \Exception('Cannot settle affiliate when loan is not active');
            }

            $loanRequest->update(
                [
                'placer_type'=>'App\Models\Affiliate',
                'placer_id'=> $affiliate->id
                ]
            );

            $loan->update(
                ['collector_type'=>'App\Models\Affiliate',
                'collector_id'=>$affiliate->id
                ]
            );  
                      
            //$employment = $user->employments()->with('employer')->get()->last();

            $user = $loan->user;
            
            // Check if theres an affiliate and credit only if this is the users first loan request
            if(!$loanRequest->affiliate_repayment_type){
                $this->settleAffiliateOnLoan($user, $loan, $financeHandler);
            }else{
                $plans = $loan->repaymentPlans->where('status',1)->where('paid_out',1);
                foreach($plans as $plan){
                    $this->settleAffiliateOnPlan($user, $plan, $financeHandler);
                }               
            }
            

            DB::commit();


            return redirect()->back()->with('success', 'affiliate successfully funded');
        }catch(\Exception $e) {


            DB::rollback();
           
            return $this->sendExceptionResponse($e);

        }
    }

  
}
