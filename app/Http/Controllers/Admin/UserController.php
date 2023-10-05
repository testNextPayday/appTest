<?php

namespace App\Http\Controllers\Admin;

use Auth, DB;
use App\Models\User;
use App\Models\Employer;
use App\Models\Investor;
use Illuminate\Support\Str;
use App\Remita\DAS\Customer;
use Illuminate\Http\Request;
use App\Traits\EncryptDecrypt;
use App\Models\WalletTransaction;
use App\Http\Controllers\Controller;
use App\Notifications\Users\AccountCreated;

class UserController extends Controller
{
    use EncryptDecrypt;
    
    public function index(Request $request)
    {
        $searchTerm = $request->get('q') ?? '';
        $users = User::query();        
        if ($searchTerm) {
            $users = $users->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }        
        $users = $users->latest()->paginate(20);
        return view('admin.borrowers.index', compact('users', 'searchTerm'));
    }
    public function getWalletBal(Request $request)
    {   
        $searchTerm = $request->get('q') ?? '';
        $users = User::query();        
        if ($searchTerm) {
            $users = $users->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }
        $users = $users->latest()->paginate(20);
        return view('admin.borrowers.wallet-balance', compact('users', 'searchTerm'));
    }

  
    public function store(Request $request)
    {
        $rule = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|size:11|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
        ];
        
        $this->validate($request, $rule);
        
        $user = new User();
        $reference = $user->generateReference();

        $password = Str::random(8);
        //send this password to the user
        
        $user = $user->create([
            'name' => $request['name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'reference' => $reference,
            'password' => bcrypt($password),
            'avatar' => 'public/defaults/avatars/default.png',
            'is_company' => $request->is_company,
            // 'adder' => 2,
            'adder_id' => Auth::id()
        ]);
        
        if($user) {
            $user->notify(new AccountCreated($password));
            return redirect()->back()->with('success', 'User added successfully');              
        }
        
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }

    public function enableSalaryNow(Request $request){
        $user = User::find($request->user_id);
        if ($user && $user->update(['enable_salary_now_loan' => $request->enable_salary_now_loan])) {            
            
            return redirect()->back()->with('success', 'Salary Now Loan Permission Updated');    
        }
        return redirect()->back()->with('failure', 'User not found');
    }
    
    public function assignStaff(Request $request)
    {
        $rules = [
            'staff_id' => 'required|exists:staff,id',
        ];
        
        $this->validate($request, $rules);
        
        if ($request->type == 'investors') {
            $user = Investor::find($request->investor_id);
        } else {
            $user = User::find($request->user_id);
        }
        
        if ($user && $user->update(['staff_id' => $request->staff_id])) {
            // DB::table('staff_user')->insert([
            //     'staff_id' => $request->staff_id,
            //     'user_id' => $request->user_id,
            //     'status' => true
            // ]);
            
            return redirect()->back()->with('success', 'Staff assigned successfully');    
        }
        return redirect()->back()->with('failure', 'User not found');
    }
    
    public function view(User $user)
    {
        $employers = Employer::where('is_primary',0)->get();
        return view('admin.borrowers.view', compact('user','employers'));
    }


    public function topUpWallet(Request $request)
    {
        $request->validate([
            'amount' => 'required',
        ]);
       $user = User::findOrFail($request->user_id);
       $user->wallet += $request->amount;
       $user->save();
       
       WalletTransaction::create([
        'owner_id' => $request->user_id,
        'owner_type' => 'App\Models\User',
        'amount' => $request->amount,
        'parties' => 1,
        'code' => 000,
        'description' => 'Cash deposit credited to wallet by admin'
       ]);

       return redirect()->back()->with('success', 'User info updated successfully');

    }



    public function updateUserInfo(Request $r)
    {
        $r->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);
        $user = User::findOrFail($r->user_id);

        $user->name = $r->name;
        $user->phone = $r->phone;
        $user->email = $r->email;
        // $user->address = $r->address;
        // $user->lga = $r->lga;
        // $user->state = $r->state;
        // $user->city = $r->city;
        $user->bvn = $r->bvn;
        $user->save();
        return redirect()->back()->with('success', 'User info updated successfully');
    }
    
    public function toggle($user_id) 
    {
        $user_id = $this->basicDecrypt($user_id);
        if(!$user_id) abort(404);
        $user = User::find($user_id);
        if($user && $user->update(['is_active' => !$user->is_active]))
            return redirect()->back()->with('success', 'User Status Changed Successfully');
        return redirect()->back()->with('failure', 'User not found');
    }
    
    public function upgrade($reference)
    {
        $user = User::whereReference($reference)->first();
        if(!$user) return redirect()->back()->with('failure', 'User not found');
        
        return view('admin.lenders_new', compact('reference'));
    }
    
    public function getSalaryInfoView()
    {
        return view('admin.borrowers.salary-data', ['salaryData' => null]);
    }

   
    
    public function getSalaryInfo(Customer $customer, Request $request)
    {
        $this->validate($request, ['phone' => 'required|min:11']);
        
        $user = User::wherePhone($request->phone)->first();
        
        if (!$user)
            return redirect()->back()->with('failure', 'User not found');
            
        $salaryData = $customer->getSalaryData($user);
        
        $gotValidData = $salaryData->status && strtolower($salaryData->status) === 'success' && $salaryData->responseCode === '00';
        
        if ($gotValidData && !$user->remita_id) {
            $user->update(['remita_id' => $salaryData->data->customerId]);
        }
        
        return view('admin.borrowers.salary-data', compact('salaryData', 'gotValidData'));
        
    }
    
    public function viewSalaryInfo(Customer $customer, User $user)
    {
        $salaryData = $customer->getSalaryData($user);
        
        $gotValidData = $salaryData && $salaryData->status && strtolower($salaryData->status) === 'success' && $salaryData->responseCode === '00';
        
        if ($gotValidData && !$user->remita_id) {
            $user->update(['remita_id' => $salaryData->data->customerId]);
        }
        
        return view('admin.borrowers.salary-data', compact('salaryData', 'gotValidData'));
        
    }

    public function upgradeSalaryPercentage(Request $request){
        $user_id = $request->user_id;
        $user = User::where('id',$user_id)->first();              
        if ($user && $user->update(['salary_percentage'=>$request->salary_percentage])) {
            return redirect()->back()->with('success', 'User Level Upgraded Successfully');    
        }
        return redirect()->back()->with('failure', 'User not found');  
    }
}
