<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Employer;
use App\Models\WalletTransaction;
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
    
    public function employerVerificationFromWallet(Request $request) 
    {
        $amount = $request->amount;
        $user = Auth::guard('web')->user();
        if ($user->wallet < $amount) {
            return response()->json(['status' => 0, 'message' => 'Insufficient wallet funds. Refresh and try again'], 200);
        }
        
        if ($user->update(['wallet' => $user->wallet - $amount])) {
            $employer = Employer::find($request->employer_id);
            $transactionData = [
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 2,
                'description' => 'Employer verification fee for '. $employer->name 
            ];
            
            WalletTransaction::create($transactionData);
            
            if($employer) {
                $employer->update(['is_verified' => 1,'user_request' => true,'user_id' => $user->id]);    
            } else {
                return response()->json([
                    'status' => 0, 
                    'message' => 'Payment was successful but employer was not found. Please contact admin to rectify this'
                ], 200);
            }    
            return response()->json(['status' => 1, 'message' => 'Payment Successful', 'employer' => $employer], 200);
        }
        
        return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again.'], 200);
        
    }
}