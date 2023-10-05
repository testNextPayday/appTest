<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LenderActivationRequest;
use App\Models\Staff;
use App\Models\StaffUser;

use App\Notifications\Users\LenderApplicationAccepted;
use App\Notifications\Users\LenderApplicationDeclined;

use App\Traits\EncryptDecrypt;
use DB;


class LenderActivationController extends Controller
{
    use EncryptDecrypt;
    
    
    public function pendingActivations()
    {
        $pending_activations = LenderActivationRequest::where('status', 2)->paginate(20);
        return view('admin.lenders_activation_pending', compact('pending_activations'));
    }
    
    public function approvedActivations()
    {
        $approved_activations = LenderActivationRequest::where('status', 1)->paginate(20);
        return view('admin.lenders_activation_approved', compact('approved_activations'));
    }
    
    public function declinedActivations()
    {
        $declined_activations = LenderActivationRequest::where('status', 0)->paginate(20);
        return view('admin.lenders_activation_declined', compact('declined_activations'));
    }
    
    public function approveRequest(Request $request)
    {
        $this->validate($request, [
            'commission_rate' => 'required'
        ]);
        
        $application = LenderActivationRequest::find($request->application_id);
        
        if($application instanceof LenderActivationRequest){
            $user = $application->user;
            
            $data = [
                'tax_number' => $application->tax_number,
                'lender_type' => $application->getOriginal('lender_type'),
                'licence_type' => $application->getOriginal('licence_type'),
                'is_managed_account' => $application->getOriginal('managed_account'),
                'licence' => $application->lender_licence,
                'is_lender' => true,
                'commission_rate' => $request->commission_rate
            ];
        
            if($user->update($data)){
                
                $application->status = 1;
                $application->update();
                
                if($user->is_managed_account){
                    $staff = Staff::where('is_active', 1)->orderBy('no_of_users', 'ASC')->first();
                    
                    if(!$staff){
                        return redirect()->back()->with('info', 'Licence activation request approved successfully. No staff was assigned!');
                    }
                    
                    $assignment = new StaffUser;
                    $assignment->staff_id = $staff->id;
                    $assignment->user_id = $user->id;
                    $assignment->status = true;
                    
                    if($oldAssignment = $assignment->assignmentExist($staff->id, $user->id)){
                        //abort(404); //firx this
                        $oldAssignment->delete();
                    }
                    
                    
                    $assignment->save();        
                    $staff->no_of_users += 1;
                    $staff->save();
                }
                $user->notify(new LenderApplicationAccepted());
                return redirect()->back()->with('success', 'Licence activation request approved successfully. Staff assigned!');
            }
            
        }
        
        else return redirect()->back()->with('failure', 'Something went wrong');
    }
    
    public function declineRequest($id)
    {
        $request_id = $this->basicDecrypt($id);
        $request = LenderActivationRequest::find($request_id);
        
        if($request instanceof LenderActivationRequest){
            $request->status = 0;
            if($request->update()){
                $user = $request->user;
                if ($user) $user->notify(new LenderApplicationDeclined());
                return redirect()->back()->with('success', 'Licence activation request declined');
            }
        }
    }
    
}