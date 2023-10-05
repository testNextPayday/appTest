<?php

namespace App\Http\Controllers\Staff;

use Auth, Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

class ProfileController extends Controller
{
    public function index()
    {
        return view('staff.profile');
    }
    
    public function update(Request $request)
    {
        $validationRules = [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'midname' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'gender' => 'required',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:1024',
        ];
        
        $this->validate($request, $validationRules);
        $user = Auth::guard('staff')->user();

        $data = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'midname' => $request->midname,
            //'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
        ];
        
        if($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if(basename($user->avatar) !== 'default.png') {
                //delete old avatar
                Storage::delete(Arr::get($user->getAttributes(), 'avatar'));
            }
            $data['avatar'] = $request->avatar->store('public/avatars');
        }
        
        if($user->update($data)) {
            return redirect()->back()->with('success', 'Profile updated successfully');
        }
        
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }


    public function updateBank(Request $request)
    {

        try {

            DB::beginTransaction();

            $this->validate($request,[
                'account_number'=>'required',
                'bank_code'=>'required'
            ]);
            
            $staff  = Auth::guard('staff')->user();
            $bank = config('remita.banks')[$request->bank_code];

            $bankDetails['bank_name'] = $bank;
            $bankDetails['account_number'] = $request->account_number;
            $bankDetails['bank_code'] = $request->bank_code;
            $bankDetails['recipient_code'] = null;

            if ($staff->banks->isNotEmpty()) {
                $staff->banks->last()->update($bankDetails);
            } else {
                $bankDetails['owner_id'] = $staff->id;
                $bankDetails['owner_type'] = get_class($staff);
                $staff->addBeneficiaryAccount($bankDetails);
            }
        } catch (\Exception $e) {

            DB::rollback();

            return redirect()->back()->with('failure',$e->getMessage());
        }

        DB::commit();

        return redirect()->back()->with('success','Bank Details Updated');
        
    }
}
