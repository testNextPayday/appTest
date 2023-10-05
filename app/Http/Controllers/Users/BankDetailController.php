<?php

namespace App\Http\Controllers\Users;

use Validator;
use App\Models\BankDetail;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BankDetailController extends Controller
{
    public function add(Request $request)
    {
        $validationRules = [
            'bank_name' => 'required', 
            'account_number' => 'required', 
        ];
        
        $validation = Validator::make($request->all(), $validationRules);
        
        if($validation->fails()) {
            return response()->json(['status' => 0, 'message' => 'Data validation failed. Please check your input and try again']);
        }
        
        $user = auth()->guard('web')->user();    
        
        $data = [
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'bank_code' => $request->bank_code
        ];
        
        
        if($bankDetail = $user->bankDetails()->create($data)) {
            return response()->json(['status' => 1, 'bankDetail' => $bankDetail], 200);
        }
        
        //return failure
        return response()->json(['status' => 1, 'message' => 'An error occurred. Please try again'], 200);
    }
    
    public function delete($bank_id)
    {
        $bank = BankDetail::find($bank_id);
        if(!$bank) return response()->json(['status' => 0, 'message' => 'Bank not found'], 200);
        
        $user = auth()->guard('web')->user();
       
        $loan = $user->updateLoans();
        if($loan){
            if($loan->canTopUp()){
                if($bank->delete()){
                    Auth::user()->update(['bvn_verified'=>false]);
                    return response()->json(['status' => 1, 'message' => 'Bank deleted successfully'], 200);
                }
                return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);
            }
            return response()->json(['status' => 0, 'message' => 'You Have Active Loan Running, Please Settle Before Profile Update'], 200);
        }

        if($user->activeLoans()->count() == 0){
            if($bank->delete()){
                Auth::user()->update(['bvn_verified'=>false]);
                return response()->json(['status' => 1, 'message' => 'Bank deleted successfully'], 200);
            }
            return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);
        }
        return response()->json(['status' => 0, 'message' => 'You Have Active Loan Running, Please Settle Before Profile Update'], 200);
    }
}
