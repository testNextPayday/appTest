<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investor;
use Carbon\Carbon;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;


class SignInController extends Controller
{
    /**
     * Automatically logins in a user with the right token access
     */
    public function authUser(Request $request)
    {
        $phone = $request->phone;
        $model = $request->model;
        $token = $request->access_token;

        if (!empty($model)):
	        $isBorrower = $model === "borrower";
	        $queryCond = [ 'api_token' => $token, 'phone' => $phone];
	        $auth_user = $isBorrower
	        	? User::where($queryCond)->first() 
	        	: Investor::where($queryCond)->first();

	        $guard = $isBorrower ? 'web' : "investor";
	        $dashboardRouteName = $isBorrower ? "users.dashboard" : "investors.dashboard";

	        if ($auth_user) {
	       		auth($guard)->login($auth_user);
	       		return redirect()->route($dashboardRouteName);
	       	}
       endif;

    	return response()->json([ 'status' => false, 'message' => 'Unauthorised User'], 401);
    }

    public function login(Request $request)
    {
        
        $rules = [
            $this->username() =>'required',
            'password'=>'required'
        ];
        
        try{

            $this->validate($request,$rules);

            if(! $this->guard()->attempt($this->credentials($request),$request->filled('remember'))){

                return response()->json(['status'=>false, 'message'=>'Invalid login attempt'],401);
            }
            $user  = Auth::user();

           
            $token = $user->createToken('Authorization Token')->accessToken;
            
            // if ($request->remember_me)
            //     $token->expires_at = Carbon::now()->addWeeks(1);
            // $token->save();
            $user->update([ 'api_token' => $token ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'
                // 'expires_at' => Carbon::parse(
                //     $personalToken->token->expires_at
                // )->toIso8601String()
            ],200);
        }catch(\Exception $e){
            
            if($e instanceof \Illuminate\Validation\ValidationException){
                return response()->json(['status'=>false,'message'=>$e->errors()],422);
            }

            return response()->json(['status'=>false,'message'=>$e->getMessage()],422);

        }
        
    }

    protected function guard()
    {
        return Auth::guard();
    }

   protected function credentials($request)
    {
        return $request->only($this->username(),'password');
    }

   protected function username()
    {
        return 'phone';
    }

    public function logout(Request $request)

    {
        try{

            $user = Auth::user();
            $user->token()->revoke();
            $user->update(['api_token'=>null]);
            //$this->guard()->logout();
            $request->session()->invalidate();

            return response()->json(['status'=>1,'message'=>' User Successfully logged out']);

        }catch(\Exception $e){

            return response()->json(['status'=>0,'message'=>$e->getMessage()]);
        }
        
    }


}
