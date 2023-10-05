<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Staff;
use App\Models\Investor;
use App\Models\Affiliate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\InvestorVerificationRequest;
use App\Notifications\Users\AccountCreated;
use App\Notifications\Investors\AccountCreated as InvestorAccountCreated;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        $investors = Investor::query();
        
        $searchTerm = $request->get('q') ?? '';
        
        if ($searchTerm) {
            $investors = $investors->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $investors = $investors->latest()->paginate(20);
        
        return view('admin.investors.index', compact('investors', 'searchTerm'));
    }

    public function activeInvestors(Request $request)
    {
        $investors = Investor::query();
        
        $searchTerm = $request->get('q') ?? '';
        
        if ($searchTerm) {
            $investors = $investors->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $investors = $investors->where('is_active', 1)->latest()->paginate(20);
        
        return view('admin.investors.active', compact('investors', 'searchTerm'));
    }
    public function inactiveInvestors(Request $request)
    {
        $investors = Investor::query();
        
        $searchTerm = $request->get('q') ?? '';
        
        if ($searchTerm) {
            $investors = $investors->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $investors = $investors->where('is_active', 0)->latest()->paginate(20);
        
        return view('admin.investors.inactive', compact('investors', 'searchTerm'));
    }
    
    public function individuals(Request $request)
    {
        $searchTerm = $request->get('q') ?? '';
        $investors = Investor::where('is_company', false);
        if ($searchTerm) {
            $investors = $investors->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $investors = $investors->latest()->paginate(20);
        return view('admin.investors.index', compact('investors', 'searchTerm'));
    }
    
    public function corporate(Request $request)
    {
        $searchTerm = $request->get('q') ?? '';
        $investors = Investor::where('is_company', true);
        if ($searchTerm) {
            $investors = $investors->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $investors = $investors->latest()->paginate(20);
        return view('admin.investors.index', compact('investors', 'searchTerm'));
    }
    
    public function view(Investor $investor)
    {
        return view('admin.investors.view', compact('investor'));
    }
    
    public function toggle(Investor $investor) 
    {
        if($investor && $investor->update(['is_active' => !$investor->is_active]))
            return redirect()->back()->with('success', 'Investor Status Changed Successfully');
        return redirect()->back()->with('failure', 'Investor not found');
    }
    
    public function update(Request $request, Investor $investor) 
    {
        if ($investor && $investor->update($request->all())) {
            return redirect()->back()->with('success', 'Updated successfully');
        }
        
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function unverified()
    {
        $investors = Investor::where('is_verified', false)
                    ->latest()->paginate(20);
        return view('admin.investors.pending', compact('investors'));
    }

    public function unAssignCommissionReceiver(Request $request, Investor $investor)
    {
        try {
            // leaving this adder type for consistency with the default value
            $investor->update(['adder_id'=>null, 'adder_type'=>'AppModelsInvestor']);

            return redirect()->back()->with('success', 'Investor unassigned');

        }catch (\Exception $e) {
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }

    public function assignCommissionReceiver(Request $request, Investor $investor)
    {
        try {

            $model = $this->determineReceivingModel($request->receiverType);

            $investor->update([
                'adder_id'=>$request->assignedPersonId,
                'adder_type'=>$model
            ]);
            
            return redirect()->back()->with('success', 'Investor commission successfully assigned');

        }catch(\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }
    }

    protected function determineReceivingModel($receiver)
    {
        switch($receiver) {
            case 'affiliate':
                $model = Affiliate::class;
            break;

            case 'staff':
                $model = Staff::class;
            break;

            case 'investor':
                $model = Investor::class;
            break;

            default:
                throw new \InvalidArgumentException('No model found');
        }

        return $model;
    }
    
    public function create()
    {
        return view('admin.investors.new');
    }
    
    public function store(Request $request)
    {
        $rules = [
            'phone' => 'required|string|size:11',
            'email' => 'required|string|email|max:255',
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
                'adder_type' => 'App\Models\Admin',
                'adder_id' => Auth::id(),
                'source_of_income'=> $request->source_of_income ?? 'Undisclosed',
                'role'=> $request->role ?? 1
            ]);
          
            if($investor) {
                $investor->notify(new InvestorAccountCreated($password));
            }
            
            DB::commit();
            session()->flash('success', 'Investor created. Please proceed to apply for investor verification');
            return redirect()->route('admin.investors.apply', ['reference' => $investor->reference]);
        } catch (Throwable $e) {
            DB::rollback();
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
        //essentially create a new user and redirect to upgrade page
    }
    
    public function showApplication($reference = null)
    {
        return view('admin.investors.apply', compact('reference'));   
    }
    
    public function apply(Request $request)
    {
        $validationRules = [
            'reference' => 'required',
            'licence_type' => 'required|integer',
            'nin_number' => 'required|string',
            'licenceImage' => 'required|image|max:20000',
            'regCertificate' => 'nullable|image|max:20000'
        ];
        
        $this->validate($request, $validationRules);
        
        
        $investor = Investor::whereReference($request->reference)->first();
        
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
            'nin_number' => $request->nin_number,
            'tax_number'=> 'Nextpayday Null',
            'licence_type' => $request->licence_type,
            'managed_account' => $managed_account,
            'status' => 2,
            'placer_type' => 'App\Models\Admin',
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
