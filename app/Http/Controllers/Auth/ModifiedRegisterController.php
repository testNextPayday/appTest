<?php

namespace App\Http\Controllers\Auth;

use Validator;
use App\Models\User;

use App\Models\Investor;
use App\Models\Affiliate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use App\Notifications\Users\EmailVerification;

class ModifiedRegisterController extends Controller
{
    protected function register(Request $request)
    {
        $rule = [
            'firstname' => 'required_if:individual,true|max:255',
            'midname' => 'nullable|string|max:255',
            'lastname' => 'required_if:individual,true|max:255',
            'phone' => 'required|string|size:11',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'company_name' => 'required_if:individual,false|max:255',
            'gender'=>''
        ];
        
        $this->validate($request, $rule);
        
        $check = $this->checkFields($request);
        
        if (!$check['status']) {
            return response()->json(['status' => 0, 'message' => $check['message']], 400);
        }

        $email_verification_code = Str::random(10);
        $password = $request['password'];
        
        $data = [
            'name' => $request['company_name'] ?? trim($request['lastname'] . ', ' . $request['firstname'] . ' ' . $request['midname']),
            'phone' => $request['phone'],
            'email' => $request['email'],
            'password' => bcrypt($password),
            'avatar' => 'public/defaults/avatars/default.png',
            'is_company' => !$request->individual,
            'email_verification_code' => $email_verification_code,
            'gender'=>$request->gender
        ];
        
        if ($affiliate = @$check['affiliate']) {
            $data['adder_type'] = Affiliate::class;
            $data['adder_id'] = $affiliate->id;
        }
        
        if ($request->borrower) {
            $guard = 'web';   
            $person = User::create($data);
        } else {
            $guard = 'investor';
            $person = Investor::create($data);
        }
        
        //send activation email
        $this->sendVerificationEmail($person, $guard);

        

        if($person && Auth::guard($guard)->attempt(['phone' => $person->phone, 'password' => $password])){

            return response()->json(['status' => 1,'message'=>'Successfully Created User !'], 200);              
        }
        
        return response()->json(['status' => 0], 200);
    }
    
    public function unverifiedEmail()
    {
        $guard = Auth::guard('investor')->check() ? 'investor' : 'web';
        
        $person = Auth::guard($guard)->user();

        return view('auth.unverified_email')->with('user',$person);
    }
    
    public function resendEmailVerification()
    {
        //find user
        $guard = Auth::guard('investor')->check() ? 'investor' : 'web';
        
        $person = Auth::guard($guard)->user();
        
        if ($person) {
            $person->update(['email_verification_code' => Str::random(10)]);
            $this->sendVerificationEmail($person, $guard);  
            return redirect()->back()->with('success', 'Verification Email Sent');    
        } else {
            return redirect()->back()->with('failure', 'We cannot send an email at this time. Please try again');
        }
        
    }
    
    private function sendVerificationEmail($person, $guard)
    {
        $person->notify(
            new EmailVerification($person->name, $person->email_verification_code, $person->email, $guard)
        );
    }
    
    public function verifyEmail($code, $email, $guard)
    {
        $query = $guard === 'investor' ? Investor::whereEmail($email) : User::whereEmail($email);
        
        $person = $query->whereEmailVerificationCode($code)->first();
        
        if ($person) {
            $person->update(['email_verified' => true]);
            
            //if user is logged in, log him out
            if (Auth::guard($guard)->check()) 
                Auth::guard($guard)->logout();
            
            //redirect to login
            $route = $guard === 'investor' ? 'investors.login' : 'login';
            return redirect()->route($route)->with('success', 'Email Verified. You can now login');
        }
        abort(404);
        
    }
    
    private function checkFields(Request $request)
    {
        $check['status'] = true;
        
        if ($request->refcode) {
            
            $affiliate = Affiliate::whereReference($request->refcode)->first();
            
            if (!$affiliate) {
                $check['status'] = false;
                $check['message'] = 'Invalid reference code.';
                return $check;    
            }
            
            $check['affiliate'] = $affiliate;
        }
        
        if ($request->borrower && $user = User::whereEmail($request->email)->first()) {
            //verify email and phone
            $check['status'] = false;
            $check['message'] = 'Email already exists';
            return $check;
        }
        
        if (!$request->borrower && $user = Investor::whereEmail($request->email)->first()) {
            //verify email and phone
            $check['status'] = false;
            $check['message'] = 'Email already exists';
            return $check;
        }
        
        if ($request->borrower && $user = User::wherePhone($request->phone)->first()) {
            //verify email and phone
            $check['status'] = false;
            $check['message'] = 'Phone number already exists';
            return $check;
        }
        
        if (!$request->borrower && $user = Investor::wherePhone($request->phone)->first()) {
            //verify email and phone
            $check['status'] = false;
            $check['message'] = 'Phone number already exists';
            return $check;
        }
        
        return $check;
    }


  
}