<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\Staff;

use App\Models\Invite;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Recipients\DynamicRecipient;
use App\Notifications\Staff\InviteNotification;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');    
    }
    
    public function index()
    {
        $staff = Staff::latest()->get();
        
        return view('admin.staff.index', compact('staff'));
    }
    
    public function view($reference)
    {
        $staff = Staff::whereReference($reference)->first();
        if(!$staff) return redirect()->back()->with('failure', 'Staff does not exist');
        return view('admin.staff.show', compact('staff'));
    }
    
    public function invites()
    {
        $invites = Invite::wherePurpose(1)->get();
        return view('admin.staff.invites', compact('invites'));
    }
    
    public function invite(Request $request)
    {
        $validationRules = [
            'email' => 'required|email|unique:staff,email'  
        ];
    
        $this->validate($request, $validationRules);
    
        $existingInvite = Invite::whereEmail($request->email)->first();
        
        if($existingInvite)
            return redirect()->back()->with('failure', 'Invite has already been sent');    
        
        $roles = array_filter(array_keys($request->all()), function($key) {
            return preg_match('/^manage_/', $key);
        });
        
        $code = Str::random(10);
        
        $data = [
            'email' => $request->email, 
            'invite_code' => $code,
            'inviter_id' => auth()->id(),
            'roles' => implode(",", $roles)
        ];
        
        if ($invite = Invite::create($data)) {
            //notify invitee
            $recipient = new DynamicRecipient($request->email);
            $recipient->notify(new InviteNotification($request->email, $code));
            return redirect()->back()->with('success', 'Invite sent successfully');
        }
        
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }

    public function toggle($reference)
    {
        $staff = Staff::whereReference($reference)->first();
        if(!$staff) abort(404);
        
        $data = ['is_active' => !$staff->is_active];
        if($staff->update($data)) {
            //return true
            return redirect()->back()->with('success', 'Status changed successfully');
        }
        //return false
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function delete($reference)
    {
        $staff = Staff::whereReference($reference)->first();
        if(!$staff) abort(404);
        //TODO: check if staff has accounts and reassign
        
        if($staff->delete()) {
            return redirect()->back()->with('success', 'Invite deleted successfully');  
        }
        //return false
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function deleteInvite($invite_id)
    {
        $invite = Invite::findOrFail($invite_id);
        
        if($invite->delete()) {
            return redirect()->back()->with('success', 'Invite deleted successfully');
        }
        //return false
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function updateRoles(Request $request, Staff $staff)
    {
        $roles = array_filter(array_keys($request->all()), function($key) {
            return preg_match('/^manage_/', $key);
        });
       
        if ($staff->update(['roles' => implode(',',$roles)])) {
            return redirect()->back()->with('success', 'Staff roles updated successfully');
        }
        
        return redirect()->back()->with('failure', 'Staff roles could not be updated');
    }
    
    public function update(Request $request, Staff $staff)
    {
        if ($staff->update($request->all())) {
            return redirect()->back()->with('success', 'Staff Updated');
        }
        
        return redirect()->back()->with('failure', 'Update not successful. Please try again');
    }
}
