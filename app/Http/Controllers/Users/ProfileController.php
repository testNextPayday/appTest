<?php

namespace App\Http\Controllers\Users;

use Hash;
use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Employer;
use App\Models\Settings;
use App\Models\BankDetail;
use App\Models\Employment;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Auth, Storage, Validator, Session;
use App\Notifications\Users\PhoneVerificationCodeNotification;
use Illuminate\Support\Arr;

class ProfileController extends Controller
{

    public function __construct(ImageService $service) 
    {
        $this->imageService = $service;
    }

    
    public function index(Request $request)
    {
        if($just_registered = $request->get('just_registered'))
            Session::flash('info', 'Welcome. Please go ahead and complete your profile');
        return view('users.profile.index');
    }
    
    public function basicUpdate(Request $request)
    {
        $now = Carbon::now();
        $eighteenYearsAgo = $now->subYears(18);

        $validationRules = [
            'name' => 'required|string',
            'email' => 'required|string|email',
            //'avatar' => 'nullable|image|mimes:jpeg,jpg,png',
            'dob' => 'required|string',
            'gender' => 'required',
            'address' => 'required|string',
            'city' => 'required|string',
            'lga' => 'required|string',
            'state' => 'required|string',
            //'passport' => 'required|image|mimes:jpeg,jpg,png|max:10240',
            //'govt_id_card' => 'required|image|mimes:jpeg,jpg,png|max:10240',
            'occupation' => 'required',
            'bvn'=>'required'
        ];
        
        $this->validate($request, $validationRules);

        $dob = Carbon::parse($request->dob);
    
        if ($dob->gt($now) || $dob->gt($eighteenYearsAgo)) {
            return redirect()->back()->with('failure', 'You\'re under the age of majority (18)');
        }
        
        $user = Auth::guard('web')->user();
        
        $data = [
            'name' => $request->name,
            'bvn' => $request->bvn,
            'gender' => $request->gender,
            'dob' => Carbon::parse($request->dob)->toDateString(),
            'address' => $request->address,
            'city' => $request->city,
            'lga' => $request->lga,
            'state' => $request->state,
            'occupation' => $request->occupation
        ];
        
        if ($request->phone && $request->phone !== $user->phone) {
            //check if user with phone number exists
            $userWithPhone = User::where('phone', $request->phone)
                                    ->where('id', '!=', $user->id)->first();

            if (!$userWithPhone) {
                $data['phone'] = $request->phone;
                $data['phone_verified'] = false;
            }

            else return redirect()->back()->with('failure', 'User with phone number already exists');
        }
        
        if($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if(basename($user->avatar) !== 'default.png') {
                //delete old avatar
                Storage::delete(Arr::get($user->getAttributes(), 'avatar'));
            }

            $url = 'public/avatars/';

            $data['avatar'] = $this->imageService->resizeImage($request->avatar, $url, [255, 255]);
        }

        $otherUrl = 'public/documents/'.$user->reference.'/';
        

        if($request->hasFile('passport') && $request->file('passport')->isValid()) {
            //delete old
            Storage::delete(Arr::get($user->getAttributes(), 'passport'));
            
            $data['passport'] = $this->imageService->compressImage($request->passport, $otherUrl);
        }
        
        if($request->hasFile('govt_id_card') && $request->file('govt_id_card')->isValid()) {
            //delete old
            Storage::delete(Arr::get($user->getAttributes(), 'govt_id_card'));
            
            $data['govt_id_card'] = $this->imageService->compressImage($request->govt_id_card, $otherUrl);
        }

        if ($user->activeLoans()->count() ) {
            return response()->json(['status' => 0, 'message' => 'You have an active loan running. Kindly Settle Before Updating Profile'], 200);
        }
        if($user->update($data)) {
            return redirect()->back()->with('success', 'Basic data updated successfully');
        }
        
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function personalUpdate(Request $request)
    {
        $now = Carbon::now();
        $eighteenYearsAgo = $now->subYears(18);
        
        $validationRules = [
            'dob' => 'required|string',
            'gender' => 'required',
            'address' => 'required|string',
            'city' => 'required|string',
            'lga' => 'required|string',
            'state' => 'required|string',
            //'passport' => 'required|image|mimes:jpeg,jpg,png|max:10240',
            //'govt_id_card' => 'required|image|mimes:jpeg,jpg,png|max:10240',
            'occupation' => 'required'
        ];
        
        $this->validate($request, $validationRules);
        $dob = Carbon::parse($request->dob);
    
        if ($dob->gt($now) || $dob->gt($eighteenYearsAgo)) {
            return redirect()->back()->with('failure', 'You\'re under the age of majority (18)');
        }
        
        $user = Auth::guard('web')->user();
        //store old phone number to determine whether user changed it
        $data = [
            'gender' => $request->gender,
            'dob' => Carbon::parse($request->dob)->toDateString(),
            'address' => $request->address,
            'city' => $request->city,
            'lga' => $request->lga,
            'state' => $request->state,
            'occupation' => $request->occupation
        ];

        $url = 'public/documents/'.$user->reference.'/';
        

        if($request->hasFile('passport') && $request->file('passport')->isValid()) {
            //delete old
            Storage::delete(Arr::get($user->getAttributes(), 'passport'));
            
            $data['passport'] = $this->imageService->compressImage($request->passport, $url);
        }
        
        if($request->hasFile('govt_id_card') && $request->file('govt_id_card')->isValid()) {
            //delete old
            Storage::delete(Arr::get($user->getAttributes(), 'govt_id_card'));
            
            $data['govt_id_card'] = $this->imageService->compressImage($request->govt_id_card, $url);
        }
        
        
        if($user->update($data)) {
            return redirect()->back()->with('success', 'Basic data updated successfully');
        }
        
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    

    public function bioUpdate(Request $request)
    {
        $validationRules = [
            'bio' => 'required',
        ];
        
        $this->validate($request, $validationRules);
        $user = Auth::guard('web')->user();
        $data = [
            'bio' => $request->bio,
        ];
        
        if($user->update($data)) {
            return redirect()->back()->with('success', 'Bio updated successfully');
        }
        
        return redirect()->back()->with('error', 'An error occurred. Please try again');
    }
    
    public function familyUpdate(Request $request)
    {
        $validationRules = [
            'marital_status' => 'required',
            'no_of_children' => 'nullable',
            'next_of_kin' => 'required',
            'next_of_kin_phone' => 'required',
            'next_of_kin_address' => 'required',
            'relationship_with_next_of_kin' => 'required'
        ];
        
        $this->validate($request, $validationRules);
        $user = Auth::guard('web')->user();
        $data = [
            'marital_status' => $request->marital_status,
            'no_of_children' => $request->no_of_children,
            'next_of_kin' => $request->next_of_kin,
            'next_of_kin_phone' => $request->next_of_kin_phone,
            'next_of_kin_address' => $request->next_of_kin_address,
            'relationship_with_next_of_kin' => $request->relationship_with_next_of_kin
        ];
        
        if($user->update($data)) {
            return redirect()->back()->with('success', 'Family profile updated successfully');
        }
        
        return redirect()->back()->with('error', 'An error occurred. Please try again');
    }
    
    public function getVerificationCode(Request $request)
    {
        if($request->phone) {
            $code = User::generateCode();
            
            //notify user
            $user = Auth::guard('web')->user();
            if($user->update(['phone_verification_code' => $code])) {
                $user->notify(new PhoneVerificationCodeNotification($code));
                return response()->json(['status' => 1], 200);    
            }
            return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);
        }   
        return response()->json(['status' => 0, 'message' => 'An error occurred. Please try again'], 200);
    }
    
    public function verifyPhoneNumber(Request $request)
    {
        $validationRules = ['code' => 'required'];
        
        $validation = Validator::make($request->all(), $validationRules);
        
        if ($validation->fails())
            return response()->json(['status' => 0, 'message' => 'Please provide code']);
        
        $user = Auth::guard('web')->user();
        if($request->code === $user->phone_verification_code && $user->update(['phone_verification' => true])) {
            return response()->json(['status' => 1, 'message' => 'Verification Successful']);
        }
        return response()->json(['status' => 0, 'message' => 'Code is incorrect. Please try again']);
    }
    
    public function updatePassword(Request $request)
    {
         
        if (!(Hash::check($request->current_password, auth()->user()->password))) {
            // The passwords matches
            return redirect()->back()->with("failure", "Your current password does not match with the password you provided. Please try again.");
        }
 
        if(strcmp($request->current_password, $request->new_password) == 0){
            //Current password and new password are same
            return redirect()->back()->with("failure", "New Password cannot be same as your current password. Please choose a different password.");
        }
        
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
 
        //Change Password
        $user = auth()->user();
        $user->password = bcrypt($request->new_password);
        $user->save();
 
        return redirect()->back()->with("success", "Password changed successfully!");   
    }

    
    /**
     * This verifies the bvn given against the account
     *
     * @param  mixed $request
     * @return void
     */
    public function verifyBVN(Request $request)
    {
        try {

            // dd("got here");

            $this->validate(
                $request, [
                'accountNumber'=>'required|string|size:10',
                'bankCode'=> 'required|size:3',
                'bvn'=> 'required|size:11',
                'user_id'=>'required'
                ]
            );

            // update the bvn as the users bvn
            $user = User::find($request->user_id);

            $user->update(['bvn'=>$request->bvn]);

            $token = config('paystack.secretKey');

            $url = 'https://api.paystack.co/bvn/match';

            $data = [
                'bvn'=> $request->bvn,
                'account_number'=> $request->accountNumber,
                'bank_code'=> $request->bankCode
            ];

            $headers = [
                'Authorization' => 'Bearer ' . $token,        
                'Accept'        => 'application/json',
            ];
            
            $client =  new Client();

            $response =  $client->post(
                $url, ['form_params'=>$data, 'headers'=>$headers]
            );

            $responseRetrieve= json_decode($response->getBody()->getContents(), true);

            if (@$responseRetrieve['data']['is_blacklisted']) {

                throw new \InvalidArgumentException('This account is blacklisted');
            }

            if (! @$responseRetrieve['data']['account_number']) {

                throw new \InvalidArgumentException("The account number returned false");
            }

            // proceed to update as verified

            $user->update(['bvn_verified'=>true]);

            return response()->json($responseRetrieve['message']);

        } catch(\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }
    }

    public function account(Request $request){
        
        return view('users.profile.password');
    }

    public function resolveCard($reference){
         
         $user = User::whereReference($reference)->first();
         $card = $user->billingCards->last();
         if($card){
            $message = 'Card Type: ' .$card->brand.', ';
            $message .= 'Bank assocoiated with Card: '.$card->bank.' and ';
            $message .= 'Card Expires in: '.$card->exp_month.'/'.$card->exp_year;
            return response()->json(['status'=> 1, 'data'=> $message], 200);
         }else{
            return response()->json(['status'=> 0, 'data'=>'Card Details not Found'], 400);
         }
         
    }
}
