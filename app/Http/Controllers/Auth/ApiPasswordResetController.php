<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Facades\OTPSms;
use App\Facades\BulkSms;
use App\Models\PhoneToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApiPasswordResetController extends Controller
{
    //

    public function resetPhone(Request $request)
    {
        

        try {
            
            DB::beginTransaction();

            $this->validate($request, [
                'phone'=> 'required'
            ]);

            //first we check if phone exist
            $user = User::wherePhone($request->phone)->first();
            
            if (! $user) {

                throw new \InvalidArgumentexception('This phone has no account linked to it');
            }

            // check if there exists a reset for this phone
            $phoneModel = PhoneToken::wherePhone($user->phone)->get()->last();

            
            if ($phoneModel) {
                
                $phoneModel->update(['token' => $phoneModel::generateCode(6) ]);
            } else {

                // Now the phone exists simply use the laravel default password reset table
                $phoneModel = PhoneToken::create(['phone'=>$user->phone]);
            }

            

            $msg  = 'Reset Token has been sent to '.$user->phone;

            $tokenMsg = ' Nextpayday reset pin '.$phoneModel->token;

            
            OTPSms::send(make_smsable($user->phone), $tokenMsg);

        }catch(\Exception $e){

            DB::rollback();

            if (
                $e instanceof \Illuminate\Validation\ValidationException
            ){

                return response()->json(['status'=>false, 'message'=>$e->errors()], 422);
            }

            return response()->json(
                ['status'=>false, 'message'=>$e->getMessage(), 'data'=>['phone'=>$request->phone]], 422
            );

        }
        DB::commit();

        return response()->json(['status'=>true, 'message'=> $msg]);
    }

    
    /**
     * Confirms the token a user enters on a phone number
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function confirmPhone(Request $request)
    {
        try{

            $this->validate($request, [
                'phone'=>'required',
                'token'=>'required'
            ]);

            // Lets find a match
            $phoneModel = PhoneToken::wherePhone($request->phone)->get()->last();

            if ($phoneModel->token != $request->token)
            {
                throw new \Exception('Token mismatch');
            }

        }catch(\Exception $e){

            if (
                $e instanceof \Illuminate\Validation\ValidationException
            ){

                return response()->json(
                    ['status'=>false, 'message'=>$e->errors()], 422
                );
            }

            return response()->json(
                ['status'=>false, 'message'=>$e->getMessage()], 422
            );

        }

        return response()->json(
            ['status'=>true, 'message'=>'Token was correct', 'data'=>['phone'=>$request->phone]]
        );
    }


    public function createPassword(Request $request)
    {
        try{

            $this->validate($request, [
                'phone'=>'required',
                'password'=>'required|confirmed',
            ]);

            $user = User::wherePhone($request->phone)->get()->first();
            
            $password = $request->password;

            $user->update(['password'=>bcrypt($password)]);

        }catch(\Exception $e){

            if (
                $e instanceof \Illuminate\Validation\ValidationException
            ){
                

                return response()->json(
                    ['status'=>false, 'message'=>$e->errors()], 422
                );
            }

            return response()->json(
                ['status'=>false, 'message'=>$e->getMessage()], 422
            );
        }

        return response()->json(
            ['status'=>true, 'message'=> 'Password Reset was successful']
        );
    }
}
