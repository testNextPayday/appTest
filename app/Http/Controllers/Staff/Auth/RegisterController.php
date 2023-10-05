<?php

namespace App\Http\Controllers\Staff\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Staff;
use App\Models\Invite;
use Carbon\Carbon;
use Auth;

class RegisterController extends Controller
{
    protected $redirectPath = '/staff';
    
    public function index()
    {
        
    }
    
    public function showInviteRegistrationForm($email, $code) 
    {
        //return view after checking requirements
        $expiry = Carbon::now()->addHours(48);
        $invite = Invite::whereEmail($email)->whereInviteCode($code)->where('created_at', '<', $expiry)->first();
        if(!$invite) {
            abort(404);
        }
        return view('auth.staff_invitation_register')->with(['email' => $email, 'code' => $code]);
    }

    public function inviteRegister(Request $request) 
    {
        $expiry = Carbon::now()->addHours(48);
        $invite = Invite::whereEmail($request->email)->whereInviteCode($request->code)->where('created_at', '<', $expiry)->first();
        if(!$invite) {
            abort(404);
        }
        $this->validator($request->all())->validate();
        $data = $request->all();
        $data['roles'] = $invite->roles;
        $staff = $this->create($data);
        $invite->delete();
        $this->guard()->login($staff);
        return redirect($this->redirectPath);
    }
    
    protected function validator(array $data) 
    {
        return Validator::make($data, [
            'firstname' => 'required|string|max:255',
            'midname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:staff',
            'email' => 'required|string|email|max:255|unique:staff',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }
    
    protected function create(array $request) 
    {
        $staff = new Staff();
        $reference = $staff->generateReference();
        $password = $request['password'];
        
        return $staff->create([
            'firstname' => $request['firstname'],
            'lastname' => $request['lastname'],
            'midname' => $request['midname'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'reference' => $reference,
            'password' => bcrypt($password),
            'avatar' => 'public/defaults/avatars/default.png',
            'roles' => $request['roles']
        ]);
    }

    protected function guard() 
    {
        return Auth::guard('staff');
    }
    
    protected function createStaff(Request $request)
    {
        $rule = [
            'firstname' => 'required|string|max:255',
            'midname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:staff',
            'email' => 'required|string|email|max:255|unique:staff',
            'password' => 'required|string|min:6|confirmed',
        ];
        
        $this->validate($request, $rule);
        $staff = new Staff();
        $reference = $staff->generateReference();
        $password = $request['password'];
        $staff = $staff->create([
            'firstname' => $request['firstname'],
            'lastname' => $request['lastname'],
            'midname' => $request['midname'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'reference' => $reference,
            'password' => bcrypt($password),
            'avatar' => 'public/defaults/avatars/default.png'
        ]);
        
        if($staff && Auth::guard('staff')->attempt(['email' => $staff->email, 'password' => $password])){
            
            // if successful, then redirect to their intended location
            return redirect()->route('staff.dashboard');
              
        }
              
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
        
    }
}