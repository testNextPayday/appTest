<?php

namespace App\Http\Controllers\Staff;

use DB, Auth, Session;
use App\Models\Investor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\InvestorVerificationRequest;
use App\Notifications\Users\AccountCreated;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        $staff = auth()->guard('staff')->user();
        $investors = $staff->investors();
        
        $searchTerm = $request->get('q') ?? '';
        
        if ($searchTerm) {
            $investors = $investors->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $investors = $investors->latest()->paginate(20);
        
        return view('staff.accounts.investors', compact('investors', 'searchTerm'));
    }
    
    public function view(Investor $investor)
    {
        return view('staff.accounts.investor', compact('investor'));
    }
    
    public function create()
    {
        return view('staff.accounts.investor_new');
    }
    
    public function store(Request $request)
    {
        $rules = [
            'phone' => 'required|string|size:11|unique:investors',
            'email' => 'required|string|email|max:255|unique:investors',
            'name' => 'required|string|max:255'
        ];
        
        $this->validate($request, $rules);
        
        DB::beginTransaction();
        
        try {
            $password = Str::random(8);
            //send this password to the user
            
            $investor = Investor::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($password),
                'is_active' => true,
                'avatar' => 'public/defaults/avatars/default.png',
                'adder_type' => 'App\Models\Staff',
                'adder_id' => Auth::id(),
                'staff_id' => Auth::id()
            ]);
            
            // DB::table('staff_user')->insert([
            //     'staff_id' => Auth::id(),
            //     'user_id' => $user->id,
            //     'status' => true
            // ]);
            
            if($investor) {
                $staff = Auth::guard('staff')->user();
                $staff->update(['no_of_users' => $staff->no_of_users + 1]);
                
                $investor->notify(new AccountCreated($password));
            }
            
            DB::commit();
            Session::flash('success', 'Investor created. Please proceed to apply for investor verification');
            return redirect()->route('staff.investors.apply', ['reference' => $investor->reference]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
        //essentially create a new user and redirect to upgrade page
    }
    
    public function showApplication($reference = null)
    {
        return view('staff.accounts.investor_apply', compact('reference'));   
    }
    
    public function apply(Request $request)
    {
        $validationRules = [
            'reference' => 'required',
            'licence_type' => 'required|integer',
            'tax_number' => 'required|string',
            'licenceImage' => 'required|image|max:20000',
            'regCertificate' => 'nullable|image|max:20000'
        ];
        
        $this->validate($request, $validationRules);
        
        
        $staff = Auth::guard('staff')->user();
        $investor = $staff->investors()->whereReference($request->reference)->first();
        
        if (!$investor) 
            return response()->json([
                'status' => 0, 
                'message' => 'Your account with reference number provided not found'
            ], 200);
        
        if ($investor->is_verified)
            return response()->json([
                    'status' => 0, 
                    'message' => 'Investor has already been verified.'
                ],
            200);
        
        if ($investor->hasPendingVerification())
            return response()->json([
                    'status' => 0, 
                    'message' => 'Investor already has a request pending.'
                ],
            200);
            
        
        $managed_account = (boolean) $request->managed_account ?? 0;
        
        $data = [
            'investor_id' => $investor->id,
            'tax_number' => $request->tax_number,
            'licence_type' => $request->licence_type,
            'managed_account' => $managed_account,
            'status' => 2,
            'placer_type' => 'App\Models\Staff',
            'placer_id' => Auth::id(),
        ];
        
        
        
        if ($request->hasFile('licenceImage') && $request->file('licenceImage')->isValid()) {
            $data['licence'] = $request->licenceImage->store('public/licences');
        }
        
        if ($request->hasFile('regCertificate') && $request->file('regCertificate')->isValid()) {
            $data['registration_certificate'] = $request->regCertificate->store('public/reg_certificates');
        }
        
        if(InvestorVerificationRequest::create($data)) {
            return response()->json([
                'status' => 1,
                'message' => 'Investor verification request sent successfully'
            ], 200);
        }
        
        return response()->json([
                'status' => 0,
                'message' => 'An error occurred. Please try again'
        ], 200);
    }
}
