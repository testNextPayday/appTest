<?php

namespace App\Http\Controllers\Affiliates;

use Carbon\Carbon;

use App\Models\User;

use App\Traits\Utilities;

use App\Models\BankDetail;
use App\Models\Employment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Notifications\Users\AccountCreated;
use Illuminate\Validation\ValidationException;

class BorrowerController extends Controller
{
    use Utilities;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth:affiliate');    
    }
    
    
    /**
     * Returns a paginated list of an affiliates borrowers
     * @author Chiemela Chinedum
     * 
     * @param Illuminate\Http\Request $request
     * 
     */
    public function index(Request $request)
    {
        $borrowers = new User();
        
        $searchTerm = $request->get('q') ?? '';
        $months = $this->getMonthsBetweenDate(now()->startOfYear());
       
        if ($searchTerm) {
            $borrowers = $borrowers->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference', 'like', '%' . $searchTerm . '%');
        }
        // $dateFilter = $request->get('date_filter') ?? '';
        // if($dateFilter){
        //     dd($dateFilter);
        // }
        
        $borrowers = $borrowers->paginate(30);
        
        return view('affiliates.borrowers.index', compact('borrowers', 'searchTerm','months'));
    }

   
    
    /**
     * Returns an affiliate's borrower
     * 
     * @param App\Models\User $user
     */
    public function show(User $user)
    {
       
        $affiliate = auth('affiliate')->user();
        
        // if (!$user->exists || (
        //     $user->adder_id !== $affiliate->id &&
        //     $user->adder_type !== get_class($affiliate)
        // )) abort(404);
        
        return view('affiliates.borrowers.show', compact('user'));
    }
    
    /**
     * Returns the borrower creation page for affiliates
     * @author Chiemela Chinedum
     */
    public function create()
    {
        return view('affiliates.borrowers.create');   
    }
    
    
    public function store(Request $request)
    {
        $rules = [
            'phone' => 'required|string|size:11|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            //'firstname' => 'required|string|max:255',
            // 'midname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required',
            //'marital_status' => 'required',
            //'no_of_children' => 'required',
            'occupation' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            //'city' => 'required|string|max:255',
            //'lga' => 'required|string|max:255',
            //'state' => 'required|string|max:255',
            // 'next_of_kin' => 'required|string|max:255',
            // 'next_of_kin_phone' => 'required|string|size:11',
            // 'next_of_kin_address' => 'required|string|max:255',
            // 'relationship_with_next_of_kin' => 'required|string|max:255',
            'employer_id' =>  'required',
            // 'position' => 'required|string|max:255',
            // 'department' => 'required|string|max:255',
            'date_employed' => 'required|date',
            'date_confirmed' => 'required|date',
            //'monthly_salary' => 'required',
            //'gross_pay' => 'required',
            'net_pay' => 'required',
            // 'supervisor_name' => 'required|string|max:255',
            // 'supervisor_email' => 'nullable|string|email',
            // 'supervisor_phone' => 'required|string|size:11',
            // 'supervisor_grade' => 'required|string|max:255',
            'bankCode' => 'required',
            'accountNumber' => 'required|size:10',
            'bvn' => 'required|size:11',
            'mda'=> 'required|string',
            'payroll_id'=> $request->payroll_id == null ? '' : 'required'
        ];
        
        try {
            
            $this->validate($request, $rules);
            
            $errors = $this->validateSecondary($request);
           

            if (count($errors)) {
                throw new \Exception("Error: ".$errors[0], 1);
                
                // return response()->json([
                //     'message' => 'Validation errors',
                //     'errors' => $errors], 422);
            }
            
            DB::beginTransaction();

            $password = Str::random(8);
            
            $user = User::create(array_merge(
                $this->generateUserData($request), 
                ['password' => bcrypt($password)]
            ));
            
            $bank = $this->createUserBank($user);
        
            $employment = $this->createUserEmployment($user, $request);
            
            //$user->notify(new AccountCreated($password));
            
            
            DB::commit();
            
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user->load('employments'),
                'employment_id' => $employment->id
            ], 200);
            
        } catch (ValidationException $e) {
            
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            
            DB::rollback();
            
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
            
        }
        
    }
    
    /**
     * Allows employment document uploads for a user
     * 
     * @param Illuminate\Http\Request $request
     * 
     */
    public function uploadDocuments(Request $request) 
    {

        try {

            $employment = $this->uploadDocumentsHandler($request);
            
            return response()->json(['status' => 1, 'employment' => $employment, 'message' => 'Upload successful'], 200);
            
        } catch (ValidationException $e) {
            
            return response()->json(['message' => $e->getMessage(), 'error' => $e->errors()], 422);
            
        } catch (\Exception $e) {
            
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

     /**
     * Handled documents upload
     *
     * @param  mixed $request
     * @return void
     */
    protected function uploadDocumentsHandler(Request $request)
    {
       
        // $rules = [
        //     // 'file' =>  'required|image|mimes:jpeg,jpg,png|max:1024',
        //     // 'label' => 'required|string',
        //     'passport' => $request->passport == null ? '' : 'image|mimes:jpeg,jpg,png',
        //     'confirmation' => $request->confirmation == null ? '' : 'image|mimes:jpeg,jpg,png',
        //     'validId' => $request->validId == null ? '' : 'image|mimes:jpeg,jpg,png',
        //     'appointment' => $request->appointment == null ? '' : 'image|mimes:jpeg,jpg,png',
        //     'workId' => $request->workId == null ? '' : 'image|mimes:jpeg,jpg,png',
        //     'employment_id' => 'required'
        // ];   
        
         
        // $this->validate($request, $rules);
        
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
    
    
    
    private function validateSecondary(Request $request)
    {
        $errors = [];
        
        $now = Carbon::now();
        
        $eighteenYearsAgo = $now->subYears(18);
        
        $dob = Carbon::parse($request->dob);
        
        
        if ($dob->gt($now) || $dob->gt($eighteenYearsAgo)) {
            array_push($errors, 'User has to be above 18');
        }
        
        //employment and confirmation date
        $date_employed = Carbon::parse($request->date_employed);
        
        $date_confirmed = Carbon::parse($request->date_confirmed);
        
        if ($date_confirmed->gt(Carbon::now()) || $date_employed->gt(Carbon::now())) {
            array_push($errors, 'Dates of confirmation and employment must be sometime in the past');
        }
        
        if ($date_confirmed->diffInMonths($date_employed) < 6 || $date_employed->gt($date_confirmed)) {
            array_push($errors, 'Date of confirmation must be at least 6 months from date of employment');
        }

         // check bvn 
         $bvn_user = User::where('bvn',$request->bvn)->first();
         $bank_user = BankDetail::where('account_number',$request->accountNumber)->first();
 
         if($bvn_user){
             array_push($errors,' User With BVN already exists');
         }
 
         if($bank_user){
             array_push($errors,' User With Account Number already exists');
         }
 
         $emp_payroll = Employment::where('payroll_id',$request->payroll_id)->first();
         $emp_mda = Employment::where('mda',$request->mda)->first();

         if(!is_null($emp_payroll) && !is_null($emp_mda)){
            if(optional($emp_payroll)->payroll_id == optional($emp_mda)->payroll_id || optional($emp_mda)->mda == $emp_payroll->mda){
                array_push($errors,' User With Employment Details already exists');
            }
         }
         
        
        return $errors;
    }
    
    
    private function generateUserData(Request $request)
    {
        return [
            //'name' => $request->surname . ', ' . $request->firstname . ' ' . $request->midname,
            'name' => $request->surname,
            'phone' => $request->phone,
            'email' => $request->email,
            'avatar' => 'public/defaults/avatars/default.png',
            'is_company' => $request->user_type,
            'adder_type' => 'App\Models\Affiliate',
            'adder_id' => auth()->id(),
            'dob' => $request->dob,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            //'no_of_children' => $request->no_of_children,
            'occupation' => $request->occupation,
            'address' => $request->address,
            //'city' => $request->city,
            //'lga' => $request->lga,
            //'state' => $request->state,
            // 'next_of_kin' => $request->next_of_kin,
            // 'next_of_kin_phone' => $request->next_of_kin_phone,
            // 'next_of_kin_address' => $request->next_of_kin_address,
            // 'relationship_with_next_of_kin' => $request->relationship_with_next_of_kin,
            'is_active' =>true,
            'bvn' => $request->bvn,
            'email_verified'=> 1
           
        ];
    }

    
    
    private function createUserBank(User $user)
    {
        //create bank details
        $data = [
            'owner_id' => $user->id,
            'owner_type' => get_class($user),
            'bank_name' => request('bankName'),
            'account_number' => request('accountNumber'),
            'bank_code' => request('bankCode')
        ];
            
        return BankDetail::create($data);
    }
    
    private function createUserEmployment(User $user, Request $request)
    {
        //employment and confirmation date
        $date_employed = Carbon::parse(request('date_employed'));
        
        $date_confirmed = Carbon::parse(request('date_confirmed'));

        $url = 'public/documents/' . $user->reference . '/employment_data/';
       
        //create employment information
        $data = [
            'user_id' => $user->id, 
            'employer_id' => request('employer_id'), 
            //'position' => request('position'), 
            'date_employed' => $date_employed->toDateString(), 
            'date_confirmed' => $date_confirmed->toDateString(), 
            // 'department' => request('department'),
            //'monthly_salary' => request('monthly_salary'), 
            //'gross_pay' => (double) str_replace(',', '', str_replace(' ', '', request('gross_pay'))), 
            'net_pay' => (double) str_replace(',', '', str_replace(' ', '', request('net_pay'))), 
            // 'supervisor_name' => request('supervisor_name'),
            // 'supervisor_email' => request('supervisor_email'),
            // 'supervisor_grade' => request('supervisor_grade'),
            // 'supervisor_phone' => request('supervisor_phone'),
            'department'=> request('department'),
            'is_current' => true,
            'mda'=>request('mda'),
            'payroll_id'=>request('payroll_id'),
            'passport'=> $request->password ? $this->imageService->compressImage($request->file('passport'), $url) : null,
            "work_id_card" => $request->workId ? $this->imageService->compressImage($request->file('workId'), $url) : null,
            "employment_letter" => $request->appointment ? $this->imageService->compressImage($request->file('appointment'), $url) : null,
            "confirmation_letter" => $request->confirmation ? $this->imageService->compressImage($request->file('confirmation'), $url) : null,
            "valid_Id" => $request->validId ? $this->imageService->compressImage($request->file('validId'), $url) : null,
            "application_form" => $request->appForm ? $request->appForm->store('documents', 's3') : null,
        ];
        
        return Employment::create($data);
    }
}
