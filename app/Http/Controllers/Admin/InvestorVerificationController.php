<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\InvestorVerificationRequest;
use App\Notifications\Users\LenderApplicationAccepted;
use App\Notifications\Users\LenderApplicationDeclined;

class InvestorVerificationController extends Controller
{
    public function pending()
    {
        $requests = InvestorVerificationRequest::where('status', 2)->paginate(20);
        return view('admin.investor-verifications.pending', compact('requests'));
    }
    
    public function approved()
    {
        $requests = InvestorVerificationRequest::where('status', 1)->paginate(20);
        return view('admin.investor-verifications.approved', compact('requests'));
    }
    
    public function declined()
    {
        $requests = InvestorVerificationRequest::where('status', 0)->paginate(20);
        return view('admin.investor-verifications.declined', compact('requests'));
    }
    
    public function approve(Request $request)
    {
        $this->validate($request, [
            'commission_rate' => 'required'
        ]);
        
        $application = InvestorVerificationRequest::find($request->application_id);
        
        if($application){
            $investor = $application->investor;
            
            $data = [
                'tax_number' => $application->tax_number,
                'licence_type' => $application->licence_type,
                'is_managed' => $application->managed_account,
                'licence' => $application->licence,
                'commission_rate' => $request->commission_rate,
                'is_verified' => true,
                'reg_cert' => $application->registration_certificate
            ];
        
            if($investor->update($data)){
                
                $application->update(['status' => 1]);
                
                if($investor->is_managed){
                   // assign staff
                }
                
                $investor->notify(new LenderApplicationAccepted());
                return redirect()->back()->with('success', 'Verification request approved successfully.');
            }
            return redirect()->back()->with('failure', 'Something went wrong');
        }
        
        else return redirect()->back()->with('failure', 'Something went wrong');
    }
    
    public function decline(InvestorVerificationRequest $investorVerificationRequest)
    {
        if($investorVerificationRequest->update(['status' => 0])){
            $investor = $investorVerificationRequest->investor;
            if ($investor) $investor->notify(new LenderApplicationDeclined());
            return redirect()->back()->with('success', 'Verification request declined');
        }
        
        return redirect()->back()->with('failure', 'Please try again');
        
    }
}
