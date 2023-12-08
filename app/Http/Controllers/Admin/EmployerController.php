<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Validation\ValidationException;

use App\Models\Employer;
use App\Models\Employment;
use App\Traits\EncryptDecrypt;
use App\PrimaryEmployer;
use App\Models\Loan;
use App\Models\LoanRequest;

use Log;

class EmployerController extends Controller
{
    use EncryptDecrypt;
    
    public function __construct()
    {
        // $this->middleware('auth:admin');
    }
    
    public function manage(Employer $employer = null) 
    {
        $primaryEmployers = PrimaryEmployer::all();
        return view('admin.employers.manage', compact('employer','primaryEmployers'));    
    }
        
    public function index(Request $request, $status = null)
    {
        $searchTerm = $request->get('q') ?? '';
        
        if ($status && $status === 'verified') {
            $employers = Employer::where('is_verified', 3);
        } else if ($status && $status === 'unverified') {
            $employers = Employer::where('is_verified', '!=', 3);
        } else {
            $employers = Employer::query();
        }
        
        if ($searchTerm) {
            $employers = $employers->where('name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('email', 'like', '%' . $searchTerm . '%');
        }
        
        $employers = $employers->latest()->get();
        
        return view('admin.employers.index', compact('employers', 'searchTerm'));
    }
    
    
    public function view(Employer $employer)
    {

        if($employer) {
            $employments = $employer->employments()->where('is_current', 1)->get();
            $requireDocs = $employer->requireDocs;
            $loanRequestDocs = $employer->loanRequestDocs;
            $loansettings = $employer->loanRequestSettings;
            
            return view('admin.employers.view', compact('employer', 'employments', 'requireDocs', 'loanRequestDocs', 'loansettings'));
        }
            
        return redirect()->back()->with('failure', 'employer not found');
    }
    
    
    public function disableEmployer($employer_id)
    {
        $employer_id = $this->basicDecrypt($employer_id);
        if(!$employer_id) abort(404);
        $employer = Employer::find($employer_id);
        if($employer && $employer->update(['is_verified' => false]))
            return redirect()->back()->with('success', 'Employer Status Changed Successfully');
        return redirect()->back()->with('failure', 'Employer not found');
    }
    
    
    public function verifyEmployer(Request $request) 
    {
        $employer = Employer::find($request->employer_id);
        if($employer && $employer->update(['is_verified' => true, 'percentage' => $request->percentage]))
            return redirect()->back()->with('success', 'Employer Status Changed Successfully');
        return redirect()->back()->with('failure', 'Employer not found');
    }
    
    
    public function setEmployerStatus(Request $request) 
    {
        $employer = Employer::find($request->employer_id);
        if($employer && $employer->update(['is_verified' => $request->status]))
            return redirect()->back()->with('success', 'Employer Status Changed Successfully');
        return redirect()->back()->with('failure', 'Employer not found');
    }
    
    /**
     * Creates a new employer
     * 
     * @param Request $request
     * @return Illuminate\Http\Response
     * 
     */
    public function store(Request $request)
    {
        $validationRules = $this->getRules();
        
        try {
            $this->validate($request, $validationRules);            
            $data = $request->only(array_keys($validationRules));
            $data['percentage'] = 10;
            $data['requireDocs'] = '{"workID":true,"validID":true,"payrollID":true,"appLetter":true,"confirmLetter":true,"bankStatement":true,"passport":true}';
            $data['loanRequestDocs'] = '{"bank_statement":true,"payslip":true}';
            $data['loanRequestSettings'] = '{"manual_loan":false}';

            $employer = Employer::create($data);            
            return response()->json([
                'message' => 'Employer added successfully', 
                'employer' => $employer], 200);
        
        } catch(ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);    
            
        } catch(\Exception $e) {
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'Employer creation failed: ' . $e->getMessage()
            ], 500);   
        }
    }
    
    
    /**
     * Updates an existing employer
     * 
     * @param Request $request
     * @param Employer $employer The employer to be updated
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, Employer $employer)
    {
        $validationRules = $this->getRules();
        
        $data = $request->only(array_keys($validationRules));
        
        if($employer->update($data)){
            return response()->json([
                'message' => 'Employer updated successfully', 
                'employer' => $employer->fresh()], 200);
        }
        
        return response()->json([
                'message' => 'Employer update failed', 
        ], 500);
    }
    
    
    public function getEmployees(Employer $employer)
    {
        $employees = [];
        
        foreach($employer->employments as $employment) {
            $user = $employment->user;
            $employees[$user->id] = $user;
            $employees[$user->id]->employment = $employment;
        }
        
        return view('admin.employers.employees', compact('employer', 'employees'));
    }
    
    
    public function getEmployeeLoans(Employer $employer)
    {
        $loans = [];
        
        $employer_ids = [];
        
        $user_ids = $employer->employments()->pluck('user_id')->toArray();
        
        $loans = Loan::whereIn('user_id', $user_ids)->get();
        
        return view('admin.employers.loans', compact('employer', 'loans'));
    }
    
    
    public function updateSweepParams(Request $request, Employer $employer)
    {
        $rules = [
            'sweep_start_day' => 'required|numeric',  
            'sweep_end_day' => 'required|numeric',  
            'sweep_frequency' => 'required|numeric',
            'peak_start_day' => 'required|numeric',  
            'peak_end_day' => 'required|numeric',  
            'peak_frequency' => 'required|numeric',
        ];    
        
        $this->validate($request, $rules);
        
        if ($employer->update($request->only(array_keys($rules)))) {
            return redirect()->back()->with('success', 'Sweep parameters updated successfully');
        }
        
        return redirect()->back()
            ->with('failure', 'Sweep parameters could not be updated. Please try again');
    }
    
    
    /**
     * Returns validation rules for creating an employer
     * 
     * @return array
     */
    private function getRules()
    {
        return [
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'state' => 'required|string',
            'is_verified' => 'required',
            'payment_mode' => 'required',
            'payment_date' => 'required',
            'employee_count' => 'required',
            'approver_name' => 'required|string',
            'approver_designation' => 'required|string',
            'approver_phone' => 'required|string',
            'approver_email' => 'required|email',
            'rate_3' => 'required|numeric',
            'rate_6' => 'required|numeric',
            'rate_12' => 'required|numeric',
            'fees_3' => 'required|numeric',
            'fees_6' => 'required|numeric',
            'fees_12' => 'required|numeric',
            'max_tenure' => 'required',
            'collection_plan' => 'required',
            'collection_plan_secondary' => 'required',
            'is_primary' => 'required',
            'vat_fee'=>'nullable|numeric',
            'loan_vat_fee'=>'nullable|numeric',
            'interest_vat_fee'=>'nullable|numeric',
            'weekly_rate_3' => 'required|numeric',
            'weekly_rate_6' => 'required|numeric',
            'weekly_rate_12' => 'required|numeric',
            'weekly_fees_3' => 'required|numeric',
            'weekly_fees_6' => 'required|numeric',
            'weekly_fees_12' => 'required|numeric',
            'max_weekly_tenure' => 'required',
            'has_weekly_repayment' => 'required|numeric'
        ];
    }

    public function documentsRequired(Employer $employer, Request $request)
    {   
        $json = json_encode($request->all());
        Employer::where('id', $employer->id)->update(["requireDocs" => $json]);
        return redirect()->back()->with("success", "Requirements updated");
    }

    public function loanLimit(Employer $employer, Request $request)
    {
       
        try{
            $this->validate($request, [
                "loanlimit"     => 'required',
                "success_fee"   => 'required',
                "application_fee" => 'required'
            ]);

        Employer::where("id", $employer->id)->update([
            "loan_limit"  => $request->loanlimit,
            "success_fee" => $request->success_fee,
            "application_fee" => $request->application_fee
            ]);

        return redirect()->back()->with("success", "Updated successfuly");

        }catch(ValidationException $e){

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422); 

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Settings failed: ' . $e->getMessage()
            ], 500); 
        }
        
    }

    public function loanDocs(Employer $employer, Request $request){        
        try {
            $loanDocs = json_encode($request->loandocs);
            $loansettings = json_encode($request->loansettings);
            $capitalize = $request->capitalize;
            $upgrade = $request->upgrade; 
            $affiliate_payment_method = $request->repayment;   
            $upfront_interest = $request->upfrontinterest;   
            $enable_guarantor = $request->enable_guarantor;            
            Employer::where('id', $employer->id)->update(["loanRequestDocs" => $loanDocs, "loanRequestSettings" => $loansettings,
             "is_capitalized"=>$capitalize,"upgrade_enabled"=>$upgrade, "affiliate_payment_method" => $affiliate_payment_method, "upfront_interest"=>$upfront_interest, "enable_guarantor" => $enable_guarantor]);
            return response()->json(["success"=>"Requirements updated"]);
        }catch (\Exception $e) {
            return response()->json(['failure'=>$e->getMessage()], 422);
        }        
    }
}
