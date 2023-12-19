<?php

namespace App\Http\Controllers\Users;

use Carbon\Carbon;
use App\Models\Employer;
use App\Models\Employment;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmploymentController extends Controller
{

    public function __construct(ImageService $service)
    {
        $this->imageService = $service;
    }


    public function create()
    {
        return view('users.employment_new');
    }
    
    public function all()
    {
        
    }
    
    public function add(Request $request)
    {
        $validationRules = [
            // 'position' => 'required', 
            // 'date_employed' => 'required|date', 
            // 'date_confirmed' => 'required|date', 
            'department' => 'required',
            'payroll_id'=>'nullable',
            'mda'=>'required',
            //'gross_pay' => 'required',
            'net_pay' => 'required',
            'employer_id' => 'required|integer',
            // 'supervisor_name' => 'required', 
            // 'supervisor_grade' => 'required',
            // 'supervisor_phone' => 'required',
            // 'supervisor_email' => 'nullable|string|email',
            
        ];
        
        $this->validate($request, $validationRules);
        
        //employment and confirmation date
        // $date_employed = Carbon::parse($request->date_employed);
        // $date_confirmed = Carbon::parse($request->date_confirmed);
        // if ($date_confirmed->gt(Carbon::now()) || $date_employed->gt(Carbon::now())) {
        //     return response()->json(['status' => 0, 'message' => 'Dates of confirmation and employment must be sometime in the past'], 200);
        // }
        
        // if ($date_confirmed->diffInMonths($date_employed) < 6 || $date_employed->gt($date_confirmed)) {
        //     return response()->json(['status' => 0, 'message' => 'Date of confirmation must be at least 6 months from date of employment'], 200);
        // }
        $user = Auth::guard('web')->user();
        
        $data = [
            'user_id' => $user->id, 
            'employer_id' => $request->employer_id, 
            //'position' => $request->position, 
            //'date_employed' => $date_employed->toDateString(), 
            //'date_confirmed' => $date_confirmed->toDateString(), 
            'department' => $request->department,
            //'monthly_salary' => $request->monthly_salary, 
            //'gross_pay' => $request->gross_pay, 
            'net_pay' => $request->net_pay, 
            // 'supervisor_name' => $request->supervisor_name,
            // 'supervisor_email' => $request->supervisor_email,
            // 'supervisor_grade' => $request->supervisor_grade,
            // 'supervisor_phone' => $request->supervisor_phone,
            'payroll_id' => $request->payroll_id,
            'mda'=>$request->mda
        ];
        if ($user->activeLoans()->count() ) {
            return response()->json(['status' => 0, 'message' => 'You have an active loan running. Kindly Settle Before Updating Profile'], 200);
            
        }
        if($employment = Employment::create($data)) {
            //return successful
            return response()->json(['status' => 1, 'employment' => Employment::find($employment->id)], 200);
        }
        
        //return failure
        return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);
    }
    
    public function uploadDocuments(Request $request) 
    {
        $rules = [
            'file' => 'required|file|mimes:jpeg,jpg,png',
            'label' => 'required|string',
            'employment_id' => 'required'
        ];   
        
        $user = Auth::guard('web')->user();
        
        $this->validate($request, $rules);
        
        $employment = Employment::find($request->employment_id);

        $url = 'public/documents/' . $user->reference . '/employment_data/';
        
        if ($employment && $employment->update([
            $request->label => $this->imageService->compressImage($request->file, $url)
            
        ])) {
            return response()->json(['status' => 1, 'employment' => $employment, 'message' => 'Upload successful'], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Upload failed'], 200);
        }
    }
    
    public function update(Request $request)
    {
        $validationRules = [
            'position' => 'required', 
            'department' => 'required',
            //'gross_pay' => 'required',
            'net_pay' => 'required',
        ];
        
        $this->validate($request, $validationRules);
        
        $user = Auth::guard('web')->user();
        
        $employment = Employment::find($request->employment_id);
        
        if (!$employment)
            return response()->json(['status' => 0, 'message' => 'Employment not found'], 200);
            
        $data = [
            'position' => $request->position, 
            'department' => $request->department,
            'payroll_id'=> $request->payroll_id,
            //'gross_pay' => (double) str_replace(',', '', str_replace(' ', '', $request->gross_pay)), 
            'net_pay' => (double) str_replace(',', '', str_replace(' ', '', $request->net_pay))
        ];
        if ($user->activeLoans()->count() ) {
            return response()->json(['status' => 0, 'message' => 'You have an active loan running. Kindly Settle Before Updating Profile'], 200);
            
        }
        if($employment->update($data)) {
            //return successful
            return response()->json(['status' => 1, 'message' => 'Update successful'], 200);
        }
        
        //return failure
        return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);
    }
    
    public function delete($employment_id)
    {
        $employment = Employment::find($employment_id);
        if(!$employment) return response()->json(['status' => 0, 'message' => 'Employment not found'], 200);
        $user = Auth::guard('web')->user();
        if ($user->activeLoans()->count() ) {
            return response()->json(['status' => 0, 'message' => 'You have an active loan running. Kindly Settle Before Updating Profile'], 200);            
        }
        $employment_letter = Arr::get($employment->getAttributes(), 'employment_letter');
        $confirmation_letter = Arr::get($employment->getAttributes(), 'confirmation_letter');
        $work_id_card = Arr::get($employment->getAttributes(), 'work_id_card');

        if ($employment_letter) {
            Storage::delete($employment_letter);
        }

        if ($confirmation_letter) {
            Storage::delete($confirmation_letter);
        }

        if ($work_id_card) {
            Storage::delete($work_id_card);
        }
        
        if($employment->delete())
        {
            return response()->json(['status' => 1, 'message' => 'Employment deleted successfully'], 200);
        }
        
        return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);   
    }
}
