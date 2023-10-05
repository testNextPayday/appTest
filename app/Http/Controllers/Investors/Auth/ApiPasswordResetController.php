<?php

namespace App\Http\Controllers\Investors\Auth;

use App\Models\Investor;
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
            $Investor = Investor::wherePhone($request->phone)->first();
            
            if (! $Investor) {

                throw new \InvalidArgumentexception('This phone has no account linked to it');
            }

            // check if there exists a reset for this phone
            $phoneModel = PhoneToken::wherePhone($Investor->phone)->get()->last();

            
            if ($phoneModel) {
                
                $phoneModel->update(['token' => $phoneModel::generateCode(6) ]);
            } else {

                // Now the phone exists simply use the laravel default password reset table
                $phoneModel = PhoneToken::create(['phone'=>$Investor->phone]);
            }

            

            $msg  = 'Reset Token has been sent to '.$Investor->phone;

            $tokenMsg = ' Nextpayday password reset pin: '.$phoneModel->token;

            
            OTPSms::send(make_smsable($Investor->phone), $tokenMsg);

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
     * Confirms the token a Investor enters on a phone number
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

            $Investor = Investor::wherePhone($request->phone)->get()->first();
            
            $password = $request->password;

            $Investor->update(['password'=>bcrypt($password)]);

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
