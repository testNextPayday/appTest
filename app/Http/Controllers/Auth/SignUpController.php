<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Facades\OTPSms;
use App\Facades\BulkSms;
use App\Models\Employer;
use App\Models\Investor;
use App\Models\Settings;
use App\Models\Affiliate;
use App\Models\Employment;
use App\Models\PhoneToken;
use App\Events\SendOTPEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Unicredit\Collection\Utilities;
use App\Http\Resources\EmployerAPIResource;
use App\Notifications\Users\EmailVerification;

class SignUpController extends Controller
{
    //
   
    public function register(Request $request)
    {
        
        try {

            $rule = $this->getRules();

            $this->validate($request, $rule);

            DB::beginTransaction();
        
            $data = $this->getUserData($request);

            $refcode = $request['ref_code'];

            if (isset($refcode)) {

                $adder = Utilities::resolveAffiliateFromCode($refcode);

                if ($adder) {
                    $data['adder_id'] = $adder->id;
                    $data['adder_type'] = get_class($adder);
                }
            }

            $user = User::create($data);

            // prevent email verification
            $user->update(['email_verification_code'=>null, 'email_verified'=>1]);
             //send activation email
            //$this->sendVerificationEmail($user, 'web');

            // if the user intends to create an employment
            
            $phoneToken = PhoneToken::where('phone', $user->phone)->first();
            if ($phoneToken) {
                $phoneToken->update([ 'verified' => true, 'token' => null ]);
            }

            Employment::create($this->getEmploymentData($user));
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json(['status'=> false,'message'=>$e->errors()], 422);
            }

            if ($e instanceof \Illuminate\Database\QueryException) {
                return response()->json(['status'=> false, 'message'=> $e->errorInfo[2]], 422);
            }
            return response()->json(['status'=> false, 'message'=>$e->getMessage()], 500);
        }
        DB::commit();

        return response()->json(['status' => 1,'message'=>'Successfully Created User !'], 200);
       
        
        return response()->json(['status' => 0], 200);
    }
    
    
    /**
     * Get User Data From Request
     *
     * @param  mixed $request
     * @return void
     */
    private function getUserData($request) 
    {
        $password = $request['password'];
        $avatar = 'public/defaults/avatars/default.png';
        if($request->avatar != null){
            $avatar = $request->avatar;
        }

        return [
                'name' =>  trim($request['lastname'] . ', ' . $request['firstname'] . ' ' . $request['midname']),
                'phone' => $request['phone'],
                'email' => $request['email'],
                'password' => bcrypt($password),
                'avatar' => $avatar,
                'gender'=> $request->gender,
                'email_verified'=> 1
            ];

    }
    
    /**
     * Gets the employment data of the user
     *
     * @param  mixed $user
     * @return void
     */
    private function getEmploymentData($user)
    {
        $work_extras = [
            'business_name'=>request()->business_name, 
            'business_address'=>request()->business_address,
            'business_desc'=> request()->business_desc
        ];

        $data =  [
            'user_id' => $user->id,
            // Eric, Please i'll be needing the ID for the Federal and RVSG workers to
            // for the `employer_id` column. I just used a fake so the 
            // registration passes.
            'employer_id' => request()->employer_id ?? Settings::salaryNowEmployerID(),
            //'monthly_salary' => request()->monthly_salary,
            'payroll_id' => request()->cworkers_payroll_id,
            'date_employed'=> request()->date_employed,
            'net_pay'=> request()->net_pay,
            'mda'=>request()->cworkers_mda
        ];

        if (isset($work_extras)) {
            $data['work_extras'] = json_encode($work_extras);
        }

        return $data;
    }
    
    /**
     * Gets all rules needed for registration
     *
     * @return array
     */
    private function getRules()
    {
        return [
            'firstname' => 'required_if:individual,true|max:255',
            'midname' => 'nullable|string|max:255',
            'lastname' => 'required_if:individual,true|max:255',
            'phone' => 'required|string|size:11|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'gender'=> 'required|numeric',
            // self employed
            'business_name' => 'required_with_all:business_desc,business_address|string|min:2',
            'business_desc' => 'required_with:business_address|string|min:20',
            'business_address' => 'required_with:business_desc|string|min:10',
            // civil workers
            'cworkers_payroll_id' => 'required_with_all:net_pay,valid_id|integer|min:5',
            //'cworkers_mda' => 'required_with:cworkers_payroll_id|string|min:2',
            // Non-civil workers
            'others_company_name' => 'required_with:company_address',
            'others_company_address' => 'required_with:others_company_name|string|min:10',
           // 'others_monthly_salary' => 'required_with:others_company_name|numeric',

            'date_employed' => 'required_with:cworkers_payroll_id,others_company_name|date',
        ];

    }

    
    public function sendToken(Request $request)
    {
        try {
            $phoneNumber = $this->validate($request, [
                'phone' => 'required|digits:11'
            ])['phone'];

            $phoneNumberExist = User::wherePhone($phoneNumber)->union(Investor::wherePhone($phoneNumber))->exists();
            if ($phoneNumberExist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone number is already in use'
                ], 422);
            }

            $phoneModel = PhoneToken::wherePhone($phoneNumber)->first();
            // if phone number already exist

            if ($phoneModel) {
                // replace the token
                $phoneModel->update(['token' => $phoneModel::generateCode(6) ]);
            } else {
                $phoneModel = PhoneToken::create([
                    'phone' => $request->phone
                ]);
            }

            $message = $this->buildMessage($phoneModel);

            $array = [
                'phone'=> $phoneNumber,
                'message'=>$message
            ];

            //event(new SendOTPEvent($array));
           
            OTPSms::send($this->readyNumber($phoneNumber), $message);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json(['status'=>false,'message'=>$e->errors()], 422);
            }
            return response()->json(['status'=>false,'message'=> $e->getMessage()], 422);
        }

        return response()->json([
            'status'=>true,
            'message'=>' An activation token has been sent to '. $phoneNumber
        ], 200);
    }


    public function readyNumber($number)
    {
        return "234" . substr(str_replace(" ", "", trim($number)), -10);
    }

    private function sendVerificationEmail($person, $guard)
    {
        $person->notify(
            new EmailVerification($person->name, $person->email_verification_code, $person->email, $guard)
        );
    }
   

    private function buildMessage($phone)
    {
        return " Your token for Nextpayday is : $phone->token";
    }


    public function verifyToken(Request $request)
    {
        $token = $request->token;
        $phoneToken = PhoneToken::whereToken($token)->first();
        
        if (!$phoneToken || ($phoneToken->phone != $request->phone)) {
            return response()->json(['status' => false, 'message' => 'Token mismatch'], 422);
        }

        try {
            $phoneToken->update([ 'token'=> null ,'verified'=>1]);
        } catch (\Exception $e) {
            return response()->json(['status'=> false, 'message'=>' Our server cannot fulfil your request now'], 422);
        }

        return response()->json(['status'=>true,'message'=>'Token was verified successfully'], 200);
    }

    public function getEmployers()
    {
        try {
            $employers = Employer::primary()->get();
            $collection = EmployerAPIResource::collection($employers);
            return response()->json(['status'=>true,'data'=>$collection], 200);
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'message'=>' An error occcured'], 422);
        }
    }

    public function checkEmail(Request $request)
    {
        try{
          	$email_address = $request->email;
            if(!$email_address) {
                return response()->json(['status'=>false,'message'=>'Invalid email address'],422);
            }

            if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)){
                return response()->json(['status'=>false,'message'=>' Email address format is invalid'],422);
            }

            $emailExists = User::where('email', $email_address)
            	->union(Investor::where('email', $email_address))->exists();
            
            if($emailExists){
                return response()->json(['status'=>false,'message'=>' Email already exists proceed to login'],422);
            }
            
            return  response()->json(['status'=>true,'message'=>'Email is valid and you can proceed'],200);
          
            
        }catch(\Exception $e){

            
            return response()->json(['status'=>false,'message'=>$e->getMessage()],500);
        }
    }
}
