<?php

namespace App\Http\Controllers\Staff;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BankDetail;
use App\Models\Employment;
use Illuminate\Support\Str;

use App\Remita\DAS\Customer;

use Illuminate\Http\Request;

use App\Services\ImageService;
use App\Traits\EncryptDecrypt;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Users\AccountCreated;

class UserController extends Controller
{
    use EncryptDecrypt;

    private $imageService;


    public function __construct(ImageService $service)
    {
        $this->imageService = $service;
    }
    
    public function index(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $users = $staff->users();

        
        $searchTerm = $request->get('q') ?? '';
        
        if ($searchTerm) {
            $users = $users->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $users = $users->paginate(20);
        
        return view('staff.accounts.users', compact('users', 'searchTerm'));
    }
    
    public function borrowers(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $users = new User();
 
        
        $searchTerm = $request->get('q') ?? '';
        
        if ($searchTerm) {
            $users = $users->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
            }
            
        
        
        $users = $users->latest()->paginate(20);
        
        return view('staff.accounts.borrowers', compact('users', 'searchTerm'));
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
            'adder' => 3,
            'adder_id' => Auth::id(),
            'email_verified'=> 1
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
            return redirect()->back()->with('success', 'User added successfully');              
        }
        
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }
    
    public function storeJSON(Request $request)
    {


            
        
        $rules = [
            'phone' => 'required|string|size:11|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'firstname' => 'required|string|max:255',
            // 'midname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required',
            'marital_status' => 'required',
            'no_of_children' => 'required',
            'occupation' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'lga' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'next_of_kin' => 'required|string|max:255',
            'next_of_kin_phone' => 'required|string|size:11',
            'next_of_kin_address' => 'required|string|max:255',
            'relationship_with_next_of_kin' => 'required|string|max:255',
            'employer_id' =>  'required',
            'position' => 'required|string|max:255',
            // 'department' => 'required|string|max:255',
            'date_employed' => 'required|date',
            'date_confirmed' => 'required|date',
            //'monthly_salary' => 'required',
           // 'gross_pay' => 'required',
            'net_pay' => 'required',
            // 'payroll_id'=> $request->payroll_id == null ? : 'required',
            'mda'=>'required',
            // 'supervisor_name' => 'required|string|max:255',
            // 'supervisor_email' => 'nullable|string|email',
            // 'supervisor_phone' => 'required|string|size:11',
            // 'supervisor_grade' => 'required|string|max:255',
            'bankCode' => 'required',
            'accountNumber' => 'required|size:10',
            'bvn' => 'required|size:11'
        ];
        
        // $this->validate($request, $rules);
        
        $now = Carbon::now();
        $eighteenYearsAgo = $now->subYears(18);
        $dob = Carbon::parse($request->dob);
        
        if ($dob->gt($now) || $dob->gt($eighteenYearsAgo)) {
            return response()->json(['status' => 0, 'message' => 'User has to be above 18'], 200);
        }
        
        //employment and confirmation date
        $date_employed = Carbon::parse($request->date_employed);
        $date_confirmed = Carbon::parse($request->date_confirmed);
        if ($date_confirmed->gt(Carbon::now()) || $date_employed->gt(Carbon::now())) {
            return response()->json(['status' => 0, 'message' => 'Dates of confirmation and employment must be sometime in the past'], 200);
        }
        
        if ($date_confirmed->diffInMonths($date_employed) < 6 || $date_employed->gt($date_confirmed)) {
            return response()->json(['status' => 0, 'message' => 'Date of confirmation must be at least 6 months from date of employment'], 200);
        }
        
        DB::beginTransaction();
        
        try {

            $password = Str::random(8);
            //send this password to the user
            
            $user = User::create([
                'name' => $request->surname . ', ' . $request->firstname . ' ' . $request->midname,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => bcrypt($password),
                'avatar' => 'public/defaults/avatars/default.png',
                'is_company' => $request->user_type,
                'adder_type' => 'App\Models\Staff',
                'adder_id' => Auth::id(),
                'staff_id' => Auth::id(),
                'dob' => $request->dob,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
                'no_of_children' => $request->no_of_children,
                'occupation' => $request->occupation,
                'address' => $request->address,
                'city' => $request->city,
                'lga' => $request->lga,
                'state' => $request->state,
                'next_of_kin' => $request->next_of_kin,
                'next_of_kin_phone' => $request->next_of_kin_phone,
                'next_of_kin_address' => $request->next_of_kin_address,
                'relationship_with_next_of_kin' => $request->relationship_with_next_of_kin,
                'is_active' =>true,
                'bvn' => $request->bvn,
                'email_verified'=> 1,
            ]);
            
            //create bank details
            $data = [
                'owner_id' => $user->id,
                'owner_type' => get_class($user),
                'bank_name' => $request->bankName,
                'account_number' => $request->accountNumber,
                'bank_code' => $request->bankCode
            ];
            
            $bankDetail = BankDetail::create($data);
        
            //create employment information
            $data = [
                'user_id' => $user->id, 
                'payroll_id'=>$request->payroll_id == null ? null : $request->payroll_id,
                'mda'=>$request->mda,
                'employer_id' => $request->employer_id, 
                'position' => $request->position, 
                'date_employed' => $date_employed->toDateString(), 
                'date_confirmed' => $date_confirmed->toDateString(), 
                'department' => $request->department,
                //'monthly_salary' => $request->monthly_salary, 
                //'gross_pay' => (double) str_replace(',', '', str_replace(' ', '', $request->gross_pay)), 
                'net_pay' => (double) str_replace(',', '', str_replace(' ', '', $request->net_pay)), 
                'supervisor_name' => $request->supervisor_name,
                'supervisor_email' => $request->supervisor_email,
                'supervisor_grade' => $request->supervisor_grade,
                'supervisor_phone' => $request->supervisor_phone,
                'is_current' => true
            ];
            
            $employment = Employment::create($data);
            
            DB::table('staff_user')->insert([
                'staff_id' => Auth::id(),
                'account_id' => $user->id,
                'account_type' => 'App\Models\User',
                'status' => true
            ]);

            
            if($user) {
                $staff = Auth::guard('staff')->user();
                $staff->update(['no_of_users' => $staff->no_of_users + 1]);
                try {
                    $user->notify(new AccountCreated($password));
                } catch(Exception $e) {
                    Log::warning($e->getMessage());   
                }
            }

            
            
            DB::commit();
            return response()->json([
                'status' => 1,
                'message' => 'User created successfully',
                'user' => $user->load('employments'),
                'employment_id' => $employment->id
            ], 200);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(['status' => 0, 'message' => $e->getMessage()], 200);   
        }
        
    }
    
    public function uploadDocuments(Request $request) 
    {
        
        try {

            $employment = $this->uploadDocumentsHandler($request);

        } catch (\Exception $e) {

            return response()->json(['status' => 0, 'message' => 'Upload failed'], 200);
           
        }

        return response()->json(['status' => 1, 'employment' => $employment, 'message' => 'Upload successful'], 200);

        
    }
    
    /**
     * Handled documents upload
     *
     * @param  mixed $request
     * @return void
     */
    protected function uploadDocumentsHandler(Request $request)
    {
       
        $rules = [
            // 'file' =>  'required|image|mimes:jpeg,jpg,png|max:1024',
            // 'label' => 'required|string',
            'passport' => $request->passport == null ? '' : 'image|mimes:jpeg,jpg,png',
            'confirmation' => $request->confirmation == null ? '' : 'image|mimes:jpeg,jpg,png',
            'validId' => $request->validId == null ? '' : 'image|mimes:jpeg,jpg,png',
            'appointment' => $request->appointment == null ? '' : 'image|mimes:jpeg,jpg,png',
            'workId' => $request->workId == null ? '' : 'image|mimes:jpeg,jpg,png',
            'employment_id' => 'required'
        ];   
        
         
        $this->validate($request, $rules);
        
        $employment = Employment::find($request->employment_id);
        
        if (!$employment) {
            throw new \InvalidArgumentException('Employment not found');
        }
        
        $user = $employment->user;

        $url = 'public/documents/' . $user->reference . '/employment_data/';

        $user->update(
            [
                'passport'=> $request->passport ? $this->imageService->compressImage($request->file('passport'), $url) : null,
            ]
        );

        return  $employment->update(
            [
                'passport'=> $request->passport ? $this->imageService->compressImage($request->file('passport'), $url) : null,
                "work_id_card" => $request->workId ? $this->imageService->compressImage($request->file('workId'), $url) : null,
                "employment_letter" => $request->appointment ? $this->imageService->compressImage($request->file('appointment'), $url) : null,
                "confirmation_letter" => $request->confirmation ? $this->imageService->compressImage($request->file('confirmation'), $url) : null,
                "valid_Id" => $request->validId ? $this->imageService->compressImage($request->file('validId'), $url) : null,
                //"application_form" => $request->appForm ? $request->appForm->store('documents', 's3') : null,
            ]
        );
    }
    
    public function view(User $user)
    {
        return view('staff.accounts.user', compact('user'));
    }
    
    public function new()
    {
        return view('staff.accounts.user_new');
    }
    
    public function upgrade($reference)
    {
        $staff = Auth::guard('staff')->user();
        $user = $staff->users()->whereReference($reference)->first();
        if(!$user) return redirect()->back()->with('failure', 'User not found');
        
        return view('staff.lenders_new', compact('reference'));
    }

    public function viewSalaryInfo(Customer $customer, User $user)
    {
        $salaryData = $customer->getSalaryData($user);
        
        $gotValidData = $salaryData && $salaryData->status && strtolower($salaryData->status) === 'success' && $salaryData->responseCode === '00';
        
        if ($gotValidData && !$user->remita_id) {
            $user->update(['remita_id' => $salaryData->data->customerId]);
        }
        
        return view('staff.accounts.borrower-salary-data', compact('salaryData', 'gotValidData'));
        
    }
    public function upgradeSalaryPercentage(Request $request){
        $user_id = $request->user_id;
        $user = User::where('id',$user_id)->first();              
        if ($user && $user->update(['salary_percentage'=>$request->salary_percentage])) {
            return redirect()->back()->with('success', 'User Level Updated Successfully');    
        }
        return redirect()->back()->with('failure', 'User not found');  
    }
}
