<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Employer;
use Validator, Auth;
use Carbon\Carbon;

class EmployerController extends Controller
{
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string',
            'phone' => 'required|string', 
            'address' => 'required|string',
            'state' => 'required|string',
            'email' => 'required|email|max:255',
            'payment_date' => 'required', 
            'payment_mode' => 'required', 
            'approver_name' => 'required|string',
            'approver_email' => 'required|email',
            'approver_designation' => 'required|string',
            'approver_phone' => 'required'
        ];
        
        $validation = Validator::make($request->all(), $validationRules);
        
        if($validation->fails()) {
            return response()->json(['status' => 0, 'message' => 'Data validation failed. Please check your input and try again']);
        }
        
        $employerData = [
            'name' => $request->name,
            'phone' => $request->phone, 
            'address' => $request->address,
            'state' => $request->state,
            'email' => $request->email,
            'payment_date' => $request->payment_date, 
            'payment_mode' => $request->payment_mode, 
            //'user_request' => true,
            //'user_id' => Auth::id(),
            'approver_name' => $request->approver_name,
            'approver_email' => $request->approver_email,
            'approver_designation' => $request->approver_designation,
            'approver_phone' => $request->approver_phone
        ];
        
        if ($employer = Employer::create($employerData)) {
            return response()->json(['status' => 1, 'employer' => $employer], 200);
        }
        
        return response()->json(['status' => 0], 200);
    }
}
