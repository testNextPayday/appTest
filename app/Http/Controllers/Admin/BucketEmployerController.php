<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Bucket;
use App\Models\Employer;

class BucketEmployerController extends Controller
{
    public function addEmployersToBucket(Request $request, Bucket $bucket)
    {
        $submissions = $request->except('_token');
        
        $success = 0; $failure = 0;
        
        foreach ($submissions as $submission) {
            $employer = Employer::find($submission);
            
            if ($employer && $employer->update(['bucket_id' => $bucket->id])) {
                $success++;
            } else {
                $failure++;
            }
            
        }
        
        return redirect()->back()->with(
            'info', 
            "$success employer(s) added to this bucket. $failure failed"
        );
    }
    
    public function removeEmployerFromBucket(Employer $employer)
    {
        if ($employer->update(['bucket_id' => null])) {
            return redirect()->back()->with('success', 'Employer removed successfully');
        }
        
        return redirect()->back()->with('failure', 'Employer could not be removed. Try again');
    }
}
