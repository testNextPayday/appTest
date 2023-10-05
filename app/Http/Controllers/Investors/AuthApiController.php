<?php

namespace App\Http\Controllers\Investors;

use Carbon\Carbon;
use App\Models\User;
use Auth, DB, Session;
use App\Models\Investor;
use App\Models\Affiliate;
use App\Models\BankDetail;
use App\Models\PhoneToken;
use Illuminate\Support\Str;

use App\Events\SendOTPEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Unicredit\Collection\Utilities;
use App\Notifications\Users\EmailVerification;


class AuthApiController extends Controller
{

    public function login(Request $request)
    {
        $rules = [
            'phone'   => 'required',
            'password' => 'required|min:6'
        ];

        try {
            $this->validate($request, $rules);

            $credentials = $request->only(['phone', 'password']);
      
            if (! Auth::guard('investor')->attempt($credentials, $request->remember)) {
                
                return response()->json(['status'=>false, 'message'=>'Invalid login attempt'],401);
            } 

            $investor = Auth::guard('investor')->user();
            $personalToken = $investor->createToken('Authorization Token');
            $token = $personalToken->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
            $investor->update(['api_token' => $personalToken->accessToken ]);

            return response()->json([
                'access_token' => $personalToken->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $personalToken->token->expires_at
                )->toIso8601String()
            ],200);
            
        }catch (\Exception $e) {

            return response()->json(['status'=>false, 'message'=>$e->getMessage()],422);
        }
    }

    public function store(Request $request)
    {
    
        try{

            $rules = $this->getRules();

            $this->validate($request, $rules);

            $data = $this->getInvestorData($request);

            $refcode = $request['ref_code'];

            if (isset($refcode)) {

                $adder = Utilities::resolveAffiliateFromCode($refcode);

                if ($adder) {
                    $data['adder_id'] = $adder->id;
                    $data['adder_type'] = get_class($adder);
                }
            }

            
            DB::beginTransaction();

                $guard = 'investor';
                $investor = Investor::create($data);
            
                $bank = [
                    'owner_id' => $investor->id,
                    'owner_type' => Investor::class,
                    'bank_name' => $this->getBankName($request->bank_code),
                    'account_number' => $request->account_number,
                    'bank_code' => $request->bank_code
                ];
           
                $bankDetail = BankDetail::create($bank);

                //send activation email
                $this->sendVerificationEmail($investor, $guard);

                // if($investor && Auth::guard($guard)->attempt(['email' => $investor->email, 'password' => $password])){
                //  }
                
                DB::commit();
                return response()->json(['status' => 1, 'message' => 'Successfully Created investors account'], 200);
            

        }catch(\Exception $e){

            DB::rollback();

            if($e instanceof \Illuminate\Validation\ValidationException){

                return response()->json(['status'=>false,'message'=> $e->errors()],422);
            }

            if ($e instanceof \Illuminate\Database\QueryException) {

                return response()->json(['status'=> false, 'message'=> $e->errorInfo[2]], 422);
            }

            return response()->json(['status'=> false, 'message'=>$e->getMessage()]);
        }

    }
    

    /**
     * Get the investor data
     *
     * @param  mixed $request
     * @return void
     */
    private function getInvestorData($request)
    {
        $email_verification_code = Str::random(10);
        $password = $request['password'];

        return [
            'name' => $request['company_name'] ?? trim($request['lastname'] . ', ' . $request['firstname'] . ' ' . $request['midname']),
            'phone' => $request['phone'],
            'email' => $request['email'],
            'password' => bcrypt($password),
            'avatar' => 'public/defaults/avatars/default.png',
            'is_company' => !$request->individual,
            'email_verification_code' => $email_verification_code,
            'auto_invest' => $request->auto_invest,
            'source_of_income' => $request->source_of_income
        ];
    }
    
    /**
     * Get request validation rules
     *
     * @return void
     */
    private function getRules()
    {
        return  [
           
            'firstname'         => 'required_if:individual,true|max:255',
            'midname'           => 'nullable|string|max:255',
            'lastname'          => 'required_if:individual,true|max:255',
            'phone'             => 'required|string|size:11|unique:investors',
            'email'             => 'required|string|email|max:255|unique:investors',
            'password'          => 'required|string|min:6|confirmed',
            'company_name'      => 'required_if:individual,false|max:255',
            'account_name'      => 'required|string',
            'account_number'    => 'required|string|size:10',
            'bank_code'         => 'required|numeric',
            'source_of_income'  => 'required|string',
            'auto_invest'       => 'required|numeric'
        ];
    }

    private function sendVerificationEmail($person, $guard)
    {
        $person->notify(
            new EmailVerification($person->name, $person->email_verification_code, $person->email, $guard)
        );
    }

    public function logout(Request $request)
    {
        try{

            $investor = Auth::guard('investor')->user();
            $investor->token()->revoke();
            $investor->update(['api_token'=>null]);
            //$this->guard()->logout();
            $request->session()->invalidate();

            return response()->json(['status'=>1,'message'=>' Investor Successfully logged out']);

        }catch(\Exception $e){

            return response()->json(['status'=>0,'message'=>$e->getMessage()]);
        }
        
    }

    public function getBankName($val)
    {
        $name = config('remita.banks.'.$val);
        return $name;
    }

    public function banks()
    {
        return config('remita.banks');
    }
}
