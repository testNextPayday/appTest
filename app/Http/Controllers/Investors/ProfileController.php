<?php

namespace App\Http\Controllers\Investors;

use Hash;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:investor');
    }
    
    public function index()
    {
        $investor = auth()->guard('investor')->user();
        return view('investors.profile.index', compact('investor'));
    }
    
    public function update()
    {
        $investor = auth()->guard('investor')->user();
        
        // Prevents unwanted data from being passed through
        $data = collect(request()->all())
                    ->intersectByKeys($investor->toArray())
                    ->toArray();
                    
        if(request()->hasFile('avatar') && request()->file('avatar')->isValid()) {
            if(basename($investor->avatar) !== 'default.png') {
                //delete old avatar
                Storage::delete(Arr::get($investor->getAttributes(), 'avatar'));
            }
            $data['avatar'] = request('avatar')->store('public/avatars');
        }
                    
        if ($investor->update($data)) {
            return redirect()->back()->with('success', 'Update successful');
        }
        
        return redirect()->back()->with('failure', 'Update failure');
    }
    
    public function bankUpdate()
    {
        $investor = auth()->guard('investor')->user();
        $data = request()->only(['bank_code', 'account_number']);
        
        $data['bank_name'] = config('remita.banks')[$data['bank_code']];
        $data['recipient_code'] = null;
        
        if ($bank = $investor->bank) {
            $bank->update($data);
        } else {
            $bank = $investor->banks()->create($data);
        }
        
        return redirect()->back()->with('success', 'Update successful');
    }
    
    public function updatePassword(Request $request)
    {
        if (!(Hash::check($request->current_password, auth()->user()->password))) {
            // The passwords matches
            return redirect()->back()->with("failure", "Your current password does not match with the password you provided. Please try again.");
        }
 
        if(strcmp($request->current_password, $request->new_password) == 0){
            //Current password and new password are same
            return redirect()->back()->with("failure", "New Password cannot be same as your current password. Please choose a different password.");
        }
        
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
 
        //Change Password
        $user = auth()->user();
        $user->password = bcrypt($request->new_password);
        $user->save();
 
        return redirect()->back()->with("success", "Password changed successfully!");   
    }
    
    public function updateDashboardType(Request $request) 
    {
        $is_managed = $request->is_managed === 'managed';
        
        if(auth()->user()->update(['is_managed' => $is_managed])) {
            return redirect()->back()->with("success", "Dashboard option updated successfully!");
        }
        
        return redirect()->back()->with('failure', "An error occurred. Please try again later");
    }


    public function setupInvestmentProfile()
    {
        $investor = auth()->guard('investor')->user();
        return view('investors.profile.investment-setup',compact('investor'));
    }

    public function saveInvestmentProfile(Request $request)
    {
        try{

            $investor = $request->user('investor');
            $investor->update($request->all());

        }catch(\Exception $e){
            if($e instanceof QueryException){
                return response()->json(['failure'=>$e->getMessage()],500);
            }
            return response()->json(['failure'=>'An error occurred'],500);
            
        }
        return response()->json(['success'=>'Profile was successfully saved'],200);
       
    }
}
