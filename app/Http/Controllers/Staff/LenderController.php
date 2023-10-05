<?php

namespace App\Http\Controllers\Staff;

use Carbon\Carbon;
use Keygen\Keygen;

use App\Models\User;
use App\Models\Settings;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LenderActivationRequest;
use App\Notifications\Users\AccountCreated;

class LenderController extends Controller
{
    public function newLender($reference = null)
    {
        return view('staff.lenders_new', compact('reference'));   
    }
    
    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $user = $staff->users()->whereReference($request->reference)->first();
        
        if (!$user) 
            return response()->json([
                'status' => 0, 
                'message' => 'Your account with reference number provided not found'
            ], 200);
        
        if ($user->is_lender)
            return response()->json([
                    'status' => 0, 
                    'message' => 'User is already a lender.'
                ],
            200);
        
        if ($user->hasPendinglenderActivationRequest())
            return response()->json([
                    'status' => 0, 
                    'message' => 'User already has a request pending.'
                ],
            200);
        
        $validationRules = [
            'reference' => 'required',
            'lender_type' => 'required|integer',
            'licence_type' => 'required|integer',
            'tax_number' => 'required|string',
            'licenceImage' => 'required|image|max:20000',
            'regCertificate' => 'required_if:lender_type,2|image|max:20000'
        ];
        
        $this->validate($request, $validationRules);
        
        $lender_request = new LenderActivationRequest;
        $managed_account = (boolean) $request->managed_account ?? 0;
        
        $reference = Keygen::numeric(6)->prefix(mt_rand(1, 9))->prefix('UC-')->generate(true);
        
        $data = [
            'user_id' => $user->id,
            'reference' => $reference,
            'tax_number' => $request->tax_number,
            'lender_type' => $request->lender_type,
            'licence_type' => $request->licence_type,
            'managed_account' => $managed_account,
            'status' => 2,
            'placer' => 3,
            'placer_id' => Auth::id()
        ];
        
        if ($request->hasFile('licenceImage') && $request->file('licenceImage')->isValid()) {
            $data['lender_licence'] = $request->licenceImage->store('public/licences');
        }
        
        if ($request->hasFile('regCertificate') && $request->file('regCertificate')->isValid()) {
            $data['reg_certificate'] = $request->regCertificate->store('public/reg_certificates');
        }
        
        if($lender_request->create($data)) {
            return response()->json([
                'status' => 1,
                'message' => 'Lenders activation request sent successfully'
            ], 200);
        }
        
        return response()->json([
                'status' => 0,
                'message' => 'An error occurred. Please try again'
        ], 200);
    }
    
    public function freshLender()
    {
        return view('staff.lenders_fresh');
    }
    
    public function saveFreshLender(Request $request)
    {
        $rules = [
            'phone' => 'required|string|size:11|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'name' => 'required|string|max:255'
        ];
        
        $this->validate($request, $rules);
        
        DB::beginTransaction();
        
        try {
            //create user
            $user = new User();
            $reference = $user->generateReference(true);

            $password = Str::random(8);
            //send this password to the user
            
            $user = $user->create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($password),
                'reference' => $reference,
                'is_active' => true,
                'avatar' => 'public/defaults/avatars/default.png',
                // 'adder' => 3,
                'adder_id' => Auth::id(),
            ]);
            
            DB::table('staff_user')->insert([
                'staff_id' => Auth::id(),
                'user_id' => $user->id,
                'status' => true
            ]);
            
            if($user) {
                $staff = Auth::guard('staff')->user();
                $staff->update(['no_of_users' => $staff->no_of_users + 1]);
                
                $user->notify(new AccountCreated($password));
            }
            
            DB::commit();
            Session::flash('success', 'Lender created. Please proceed to complete lender application');
            return redirect()->route('staff.lenders.new', ['reference' => $reference]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
        //essentially create a new user and redirect to upgrade page
    }
}
