<?php

namespace App\Http\Controllers\Investors;

use DB;
use Validator;
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
use App\Models\InvestWithMono;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\InvestmentTransaction;
use App\Models\PromissoryNoteSetting;
use Illuminate\Support\Facades\Session;
use App\Notifications\Investors\AccountCreated;
use App\Services\Investor\PromissoryNoteService;
use App\Services\MonoStatement\BaseMonoStatementService;
use App\Unicredit\Collection\Utilities;

class PaydayNoteSignupController extends Controller
{
    protected $monostatementService;
    protected $promissoryService;

    public function __construct(BaseMonoStatementService $bankStatementService, PromissoryNoteService $promissoryService)
    {
        $this->monostatementService = $bankStatementService;
        $this->promissoryService = $promissoryService;
    }

    public function index(){
        return view('investors.index');
    }

    public function verifyContact(){
        return view('investors.register');
    }

    public function loginPage(){
        return view('investors.login');
    }

    public function verifyOTP(){
        return view('investors.verify-otp');
    }

    public function sendToken(Request $request)
    {
        try {
            $phoneNumber = $this->validate($request, [
                'phone' => 'required|digits:11'
            ])['phone'];

            

            $phoneNumberExist = Investor::wherePhone($phoneNumber)->exists();
            if ($phoneNumberExist) {
                
                //Session::flash('error', ' Phone Number Is Already In Use');

                return redirect()->route('investors.signup')->with('error',  'Phone Number Is Already In Use');
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

            

            //event(new SendOTPEvent($array));
           
            if(OTPSms::send($this->readyNumber($phoneNumber), $message)){
                $request->session()->put('phone', $phoneNumber);
                Session::flash('success', ' An activation token has been sent to '. $phoneNumber);
                return redirect()->route('investors.enter.otp');


                //return redirect()->route('investors.enter.otp')->with('success',  ' An activation token has been sent to '. $phoneNumber);
            }
            return redirect()->back()->with('error',  ' Operation Failed ');
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {                
                   
                return redirect()->back()->with('error',  $e->getMessage());
                
            }
            return redirect()->back()->with('error',  $e->getMessage());
        }        
        
    }


    public function readyNumber($number)
    {        
        return "234" . substr(str_replace(" ", "", trim($number)), -10);
    }

    private function buildMessage($phone)
    {
        return " Your token for Nextpayday is : $phone->token";
    }


    public function verifyToken(Request $request)
    {
        $token = $request->token;
        $phoneToken = PhoneToken::whereToken($token)->first();
        
        if (!$phoneToken) {
            return redirect()->route('investors.enter.otp')->with('error', ' Token mismatch ');            
        }

        try {
            $phoneToken->update([ 'token'=> null ,'verified'=>1]);
        } catch (\Exception $e) {
            return redirect()->route('investors.enter.otp')->with('error', ' Our server cannot fulfil your request now ');
        }

        Session::flash('success', ' OTP Verified, Please Complete The Process ');        
        return redirect()->route('investors.enter.profile.data');
    }

    /**
     * Show the personal info Form for creating a new investor.
     *
     * @return \Illuminate\Http\Response
     */
    public function basicProfile(Request $request)
    {
        $profile = $request->session()->get('personalData');  
        return view('investors.basic-profile',compact('profile'));
    }
    
  
    /**  
     * Post Request to store profile data info in session
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postProfileInfo(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'phone' => 'required|string|size:11|unique:investors',
            'email' => 'required|string|email|max:255|unique:investors',
            'name' => 'required|string|max:255',
            'role'=> 'required',
            'password' => 'required|string|min:6',
            'ref_code' => 'nullable'
         ]);


        if($validation->fails()){
            return redirect()->route('investors.enter.profile.data')->with('error', $validation->errors());  
        }
        //Gets user's data after validation
        $investorData = $request->all();

        //Encrypts user's password
        $investorData['password'] = bcrypt($investorData['password']);
  
        if(empty($request->session()->get('profileData'))){
            $profile = new Investor();
            $profile->fill($investorData);
            $request->session()->put('profileData', $profile);
        }else{
            $profile = $request->session()->get('profileData');
            $profile->fill($investorData);
            $request->session()->put('profileData', $profile);
        }
  
        return redirect()->route('investors.enter.bank.details');
    }

    /**
     * Show the bank details Form for creating a new investor.
     *
     * @return \Illuminate\Http\Response
     */
    public function bankDetails(Request $request)
    {
        $profile = $request->session()->get('personalData');  
        return view('investors.bank-details',compact('profile'));
    }

    public function login(Request $request)
    {
        try {

            $login = $request->validate([
                'phone'   => 'required',
                'password' => 'required|min:6'
            ]);

            $credentials = $request->only(['phone', 'password']);
      
            if (Auth::guard('investor')->attempt($credentials)) {
                return redirect()->intended(route('investors.dashboard'));
            }     
            
            return redirect()->route('investors.signup')->with('error', ' Invalid Credntials');
           
        } catch (\Exception $e) {

            return redirect()->route('investors.signup')->with('error', $e->mesage());
        }
    }

    public function store(Request $request)
    { 
        
        DB::beginTransaction();
        
        try {
            
            $this->validate($request, [
                'bank_code'=> 'required|max:3|string|size:3',
                'account_number'=> 'required|size:10'
            ]);

            $password = $request->password;
            $refcode = $request->ref_code;
            
            if (isset($refcode)) {

                $adder = Utilities::resolveAffiliateFromCode($refcode);

                if ($adder) {
                    $request->adder_id = $adder->id;
                    $request->adder_type = get_class($adder);
                }
            }
            
            $investor = Investor::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($password),
                'is_active' => true,
                'avatar' => 'public/defaults/avatars/default.png',
                'adder_type' => $request->adder_type ?? null,
                'adder_id' => $request->adder_id ?? null,
                'source_of_income'=> $request->source_of_income ?? 'Undisclosed',
                'role'=> $request->role ?? 1
            ]);

            

            $investor  = Investor::find($request->investor_id);

            $data = $request->only(['account_number', 'bank_code']);

            $response = $this->promissoryService->createBankDetails($investor, $data);

            if ($response) {

                return redirect()->back()->with('success', 'Bank details updated');
            }

            return redirect()->back()->with('failure', 'An error occured. Try again');
          
            if($investor) {
                $investor->notify(new InvestorAccountCreated($password));
            }
            
            DB::commit();
            Session::flash('success', 'Investor created. Please proceed to apply for investor verification');
            return redirect()->route('admin.investors.apply', ['reference' => $investor->reference]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
        //essentially create a new user and redirect to upgrade page
    }

    public function store2(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'phone' => 'required|string|size:11|unique:investors',
            'email' => 'required|string|email|max:255|unique:investors',
            'name' => 'required|string|max:255',
            'role'=> 'required',
            'password' => 'required|string|min:6',
            'ref_code' => 'nullable'
         ]);


        if($validation->fails()){
            return redirect()->route('investors.enter.profile.data')->with('error', $validation->errors());  
        }
        
        DB::beginTransaction();
        
        try {
            $password = $request->password;
            $refcode = $request->ref_code;
            
            if (isset($refcode)) {

                $adder = Utilities::resolveAffiliateFromCode($refcode);

                if ($adder) {
                    $request->adder_id = $adder->id;
                    $request->adder_type = get_class($adder);
                }
            }
            
            $investor = Investor::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($password),
                'is_active' => true,
                'avatar' => 'public/defaults/avatars/default.png',
                'adder_type' => 'App\Models\Investor' ?? 'App\Models\Admin',
                'adder_id' => 0 ?? Auth::id(),
                'source_of_income'=> $request->source_of_income ?? 'Undisclosed',
                'role'=> $request->role ?? 1,
                'is_verified'=> 1
            ]);
          
            if($investor) {
                $investor->notify(new AccountCreated($password));                
            }
            
            DB::commit();
            
            return redirect()->route('investors.signin')->with('success', 'Account Created Successfully Please SIgn In');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
        //essentially create a new user and redirect to upgrade page
    }

    public function monoDirectPay(Request $request){
        DB::beginTransaction();
            try {
                $amount = $request->amount;
                
                $startDate = $request->startDate;
                $tenure = $request->tenure;
                $interestPaybackCycle = $request->interestPaybackCycle;
                $investor = auth()->guard('investor')->user();
                
                $my_array = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $my_arrays = str_shuffle($my_array);
                $my_arrayz = str_shuffle($my_array);
                $reference = rand(0000,9000).substr($my_arrays,25).rand(0,9).rand(1000,9000).substr($my_arrayz,25);
                
                
                //initiating mono payment
                $this->monostatementService->investorFund($amount, $reference);
                $monoInfo = $this->monostatementService->getResponse();
                
                if($monoInfo){
                    $reference = $monoInfo['reference'];                    
                    $paymentLink = $monoInfo['payment_link'];                    
                    
                    InvestWithMono::create([
                        'amount' => $amount,
                        'start_date' => $startDate,
                        'tenure' => $tenure,
                        'interest_payback_cycle' => $interestPaybackCycle,
                        'reference' => $reference,
                        'investor_id' => $investor->id,
                        'payment_type'=>'mono',
                    ]);    

                    DB::commit();     
                    return response()->json(['status' => 1, 'paymentLink' => $paymentLink], 200);
                }
                return response()->json(['status' => 0, 'message' => 'Payment Failed, Try Again'], 400);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
            }
    }

    public function monoStatus(){
        return view('investors.promissory-notes.verify-monostatus');
    }
    public function verifyMonoStatus(Request $request){
        try {
            DB::beginTransaction();

            $reference = $request->input('reference');            
            $this->monostatementService->verifyPaymentStatus($reference);
            $response = $this->monostatementService->getResponse();
            $status = $response['data']['status'];
           
            if($status === 'successful'){
                $investment = InvestWithMono::where('reference', $reference)->first();  
                if($investment->interest_payback_cycle == 'upfront'){
                    $settings = PromissoryNoteSetting::where('name', 'like','%Upfront%')->first();
                    $rate = $settings->interest_rate;
                    $tax = $settings->tax_rate;
                }
                if($investment->interest_payback_cycle == 'backend'){
                    $settings = PromissoryNoteSetting::where('name', 'like','%Backend%')->first();
                    $rate = $settings->interest_rate;
                    $tax = $settings->tax_rate;
                }
                if($investment->interest_payback_cycle == 'monthly'){
                    $settings = PromissoryNoteSetting::where('name', 'like','%Monthly%')->first();
                    $rate = $settings->interest_rate;
                    $tax = $settings->tax_rate;
                }
                              
                $data = [
                    'start_date' => $investment->start_date,
                    'amount' => $investment->amount,
                    'tenure' => $investment->tenure,
                    'investor_id'=> $investment->investor_id,
                    'interest_payment_cycle'=>$investment->interest_payback_cycle,
                    'rate'=> $rate,
                    'tax'=> $tax,
                    'payment_type'=>$investment->payment_type,
                    'invest_id'=>$investment->id,
                ];
                if (!$investor = Investor::find($investment->investor_id)) {
                    throw new \InvalidArgumentException('Could not find Investor');
                }
    
                if ($response = $this->promissoryService->createPromissoryNote2($data, $investor)) {
    
                    DB::commit();
                    return redirect()->route('investors.promissory-note.fund.account')->with('success', 'Promissory Note is Pending Approval');
                    
                }
            }
            return redirect()->back()->with(['status'=>false, 'message'=>'Payment Status Could Not Be Verified'], 400);
        }catch(\Exception $e) {
            DB::rollback();
            return response()->json(['status'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    public function verifyStatus(){
        try {
            $reference = $request->reference;
            $this->monostatementService->verifyPaymentStatus($reference);
            $response = $this->monostatementService->getResponse();
            $status = $response['data']['status'];        
            $transaction = InvestmentTransaction::where('reference', $reference)->first();            
           
            if($status == 'active'){
                $transaction->update(['verification_status'=>$stataus]);
                return response()->json(['status'=>true, 'message'=>'Your Payment Is Verified, Please Wait For Admin To Approve'], 200);
            }
            return response()->json(['status'=>false, 'message'=>'Payment Status Could Not Be Verified'], 400);
        }catch(\Exception $e) {
            return response()->json(['status'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    public function investorBankStore(Request $request)
    {
        try {

            $this->validate($request, [
                'bank_code'=> 'required|max:3|string|size:3',
                'account_number'=> 'required|size:10'
            ]);

            $investor  = Investor::find($request->investor_id);

            $data = $request->only(['account_number', 'bank_code']);

            $response = $this->promissoryService->createBankDetails($investor, $data);

            if ($response) {

                return redirect()->back()->with('success', 'Bank details updated');
            }

            return redirect()->back()->with('failure', 'An error occured. Try again');

        }catch(\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }
    }
}
