<?php

namespace App\Http\Controllers\Admin;

use App\Models\Affiliate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AffiliateSettingsController extends Controller
{
    //
    
    /**
     *  Method to update an affiliate settings
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Affiliate $affiliate
     * @return  \Illuminate\Http\Response
     */
    public function configureSettings(Request $request, Affiliate $affiliate)
    {
    

        try {
            $data = $request->only(
                ['loan_vissibility', 'commission_method', 'booking_status']
            );
            
            $update = $affiliate->update(['settings'=>json_encode($data)]);

            $employer = $request->input('employer_id');
            $mappedEmployer = $affiliate->mappedEmployer()->sync($employer);
            if($update && $mappedEmployer){
                return redirect()->back()->with('success', 'Affiliate Settings Updated');             
            }  
            
        } catch(\Exception $e) {
            return $this->sendExceptionResponse($e);
        }

    }
    
}
