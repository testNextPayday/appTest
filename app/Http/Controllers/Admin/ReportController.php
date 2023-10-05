<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ActiveLoanExport;
use App\Exports\EmployersExport;
use DB;
use App\Models\Loan;
use App\Models\Employer;
use Carbon\Carbon;
use App\Models\Investor;
use App\Models\LoanFund;
use App\Models\Affiliate;
use App\Models\Repayment;
use Illuminate\Http\Request;
use App\Models\RepaymentPlan;
use App\Models\WalletTransaction;
use App\Http\Controllers\Controller;
use App\Traits\Managers\ReportManager;
use Illuminate\Database\QueryException;
use App\Notifications\Users\LoanRepaymentNotification;
use App\Statistics\Investor as InvestorStats;
use App\Services\Reports\InvestorReportService;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    //
    use ReportManager;

    public function index()
    {
        $employers =  Employer::where('is_primary', 0)->get(['id', 'name']);
        return view('admin.reports.index', compact('employers'));
    }

    public function downloadActiveLoan(Request $request)
    {
        // dd('test');
        $request->validate(
            ['employer_id' => 'required',
            'fromDate' => 'required',]
        );

        $loans = Employer::find($request->employer_id)
            ->employeeLoansQuery()
            ->active()
            ->with('user')
            ->whereDate('created_at', '>=', $request->fromDate)
            ->get();
        

        return view('admin.reports.employers', compact('loans'));

        // return Excel::download(new ActiveLoanExport($request->employer_id, $request->fromDate), 'activeLoad.xlsx' );
    }


    public function getPrimaryEmployers()

    {
        $employers =  Employer::where('is_primary', 0)->get(['id', 'name']);
        return response()->json($employers, 200);
    }

    public function getAffiliates()
    {
        $affiliates = Affiliate::all(['id', 'name']);
        return response()->json($affiliates, 200);
    }

    public function getInvestors()
    {
        $investors = Investor::all(['id', 'name']);
        return response()->json($investors, 200);
    }

    public function sendNotification(Request $request){
        
        try{
                DB::beginTransaction();
                $loanReferences = $request->notifyArr;        
                $unpaidLoan = Loan::whereIn('reference', $loanReferences)->get();        
                foreach($unpaidLoan as $loan){            
                    $user = $loan->user;
                    
                    foreach($loan->unpaidDueRepayments() as $duePayments){
                        
                        $due_emis = $duePayments->total_amount;
                    }  
                        
                    $unpaidAmount = $due_emis; 
                        
                $user->notify(new LoanRepaymentNotification($loan, $unpaidAmount));
                return response()->json(['status'=>1,'message'=>'Users Successfully Notified' ],200);
                    
                }
                
            }catch(\Exception $e) {
                DB::rollback();
                return response()->json([ 'status'=> 0,'message'=>$e->getmessage()], 400);
            }
            DB::commit();
            
    }

    public function getStatistics(Request $request)
    {
        $name = $request->name;
        switch ($name) {

            case 'loans-disbursed':
                $loans = Loan::all(['id', 'amount']);
                $data = array(
                    number_format($loans->count()),
                    number_format($loans->sum('amount'), 2)
                );
                return response()->json($data, 200);
                break;
            case 'collections-made':
                $data = array();
                return response()->json($data, 200);
                break;
            case 'fees-earned':
                $repaymentPlans = RepaymentPlan::all(['management_fee', 'id']);
                $data = array(
                    number_format($repaymentPlans->count()),
                    number_format($repaymentPlans->sum('management_fee'), 2)
                );
                return response()->json($data, 200);
                break;
            case 'commissions-given':
                $commissions = WalletTransaction::commissions()->get(['id', 'amount']);
                $data = array(
                    number_format($commissions->count()),
                    number_format($commissions->sum('amount'))
                );
                return response()->json($data, 200);
                break;
            case 'active-loans':
                $loans = Loan::active()->get(['id', 'amount']);
                $data = array(
                    number_format($loans->count()),
                    number_format($loans->sum('amount'), 2)
                );
                return response()->json($data, 200);
                break;
            case 'penalties':
                $data = array();
                return response()->json($data, 200);
                break;
            case 'insurances':
                $loans  = Loan::all(['id', 'amount'])->each(function ($item, $index) {
                    $item->insurance = $item->amount * (2.5 / 100);
                });
                $data = array(
                    number_format($loans->count()),
                    number_format($loans->sum('insurance'), 2)
                );
                return response()->json($data, 200);
                break;
            case 'investments':
                $loanFunds = LoanFund::all(['id', 'amount']);
                $data = array(
                    number_format($loanFunds->count()),
                    number_format($loanFunds->sum('amount'), 2)
                );
                return response()->json($data, 200);
                break;
            case 'repayments':
                $repayments  = Repayment::all();
                $data = array(
                    number_format($repayments->count()),
                    number_format($repayments->sum('amount'), 2)
                );
                return response()->json($data, 200);
                break;

            case 'portfolio-fees':

                $fees = WalletTransaction::portfolioFees()->get(['id', 'amount']);
                $count = number_format($fees->count(), 2);
                $values = number_format($fees->sum('amount'), 2);
                $data = array($count,$values);
                return response()->json($data, 200);
                break;
            case 'user-portfolios':
                $repayments  = RepaymentPlan::whereStatus(0)->get();
                $data = array(
                    number_format($repayments->count()),
                    number_format($repayments->sum('amount'), 2)
                );
                return response()->json($data, 200);                
                break;

            default:
                return response()->json(
                ['message' => 'Error no parameter specified'], 404
             );
        }
    }

    

    public function fetchData(Request $request)
    {

        try {
            list($info, $datas) =  $this->discoverReport($request);
        } catch (\Exception | QueryException $e) {
            return response()->json(
                [
                    'message' => $e->getMessage()
                ],
                404
            );
        }

        return response()->json(['info' => $info, 'result' => $datas], 200);
    }

    public function discoverReport($request)
    {
        switch ($request->code) {
            case '001':
                return $this->LoanDisbursementReport($request);
                break;
            case '002':
                return $this->CollectionsReport($request);
                break;
            case '003':
                return $this->FeesEarnedReport($request);
                break;
            case '004':
                return $this->CommissionsGivenReport($request);
                break;
            case '005':
                return $this->ActiveLoansReport($request);
                break;
            case '006':
                return $this->PenaltyReport($request);
                break;
            case '007':
                return $this->InsuranceReport($request);
                break;
            case '008':
                return $this->InvestmentReport($request);
                break;
            case '009':
                return $this->InvestorReport($request);
                break;
            case '010':
                return $this->RepaymentConfirmedReport($request);
            break;
            case '011':
                return $this->UserPortfolioReport($request);
            break;
            default:
                return $this->LoanDisbursementReport($request);
        }
    }


    public function LoanDisbursementReport($request)
    {
        $from = $request->data['from'];
        $to = $request->data['to'];
        $loantype  = $request->data['loantype'];

        $employer_id = $request->data['employer'];
        

        if (!is_null($employer_id)) {
            $employer = Employer::find($employer_id);
            $activeLoans  = $employer->employeeLoansQuery() 
                ->whereBetween('created_at', [$from, $to])
                ->get(['reference', 'disbursal_amount', 'created_at', 'request_id', 'user_id', 'emi','duration', 'is_top_up'])
                ->each(function ($item, $index) {
                    //$item->employer = $item->employer_name;
                    $item->user = $item->user;
                    @$item->mda = optional($item->loanRequest->employment)->mda;
                    @$item->payroll_id = optional($item->loanRequest->employment)->payroll_id;
                    @$item->amount = (float)$item->disbursal_amount ?? $item->amount;
                   
                });
            $info = "For  " . $employer->name . ":";
        }else{
            $activeLoans = Loan::with('user')->whereBetween('created_at', [$from, $to])
            ->get(['reference', 'disbursal_amount', 'request_id', 'created_at', 'user_id', 'emi','duration', 'is_top_up'])
            ->each(function ($item, $index) {
                //$item->employer = ($item->loanRequest) ? $item->employer_name : null;
                @$item->amount = (float)$item->disbursal_amount ?? $item->amount;
                @$item->mda = optional($item->loanRequest->employment)->mda;
                @$item->payroll_id = optional($item->loanRequest->employment)->payroll_id;
               
            });
        $info = "For all Employers:  ";
        }
        
        if ($loantype == 'topups') {

            $activeLoans = $activeLoans->filter(function($loan) { return $loan->is_top_up == 1;});
        }
        $info .= " Total Amount Disbursed is ₦" . number_format($activeLoans->sum('amount'), 2);
        $info .= " Total Number of Loans disbursed is " . $activeLoans->count();

        return [$info, $activeLoans];
    }

    public function RepaymentConfirmedReport($request)
    {
        $from = $request->data['from'];
        $to = $request->data['to'];
        
        $confirmedPayments = RepaymentPlan::with('loan.user.employments')->whereStatus(1)->whereBetween('updated_at', [$from, $to])
                        ->get(['paid_amount','loan_id', 'status', 'emi', 'management_fee', 'updated_at'])->each(function($item, $index) {
                            $item->amount = $item->paid_amount ? $item->paid_amount : $item->emi + $item->management_fee;
                            if(isset($item->loan)) {
                                if($itemOwner = $item->loan->user){
                                    if($itemOwner->employments->count() > 0) {
                                        $item->payroll_id = $itemOwner->employments->first()->payroll_id;
                                    }
                                }
                            }
                        })->filter(function($plan) { return isset($plan->loan);});

        $info = "For all Employers:  ";
     
        $info .= " Total Amount Confirmed is ₦" . number_format($confirmedPayments->sum('amount'), 2);
        $info .= " Total Number of Plans Confirmed is " . $confirmedPayments->count();

        return [$info, $confirmedPayments];
    }


    public function CollectionsReport($request)
    {
        $from = $request->data['from'];
        $to = $request->data['to'];

        $criteria = $request->data['criteria'];
        switch($criteria){
            case null:
                $collections = RepaymentPlan::whereBetween('payday', [$from, $to])
                ->with('loan.user')->get()
                ->filter(function($item){
                    return $item->loanActive();
                })
                ->each(function($item,$index){
                    $employment = $item->loan->user->employments->first();
                    $collector = $item->loan->collector_type == 'AppModelsUser' ? null : $item->loan->collector;

                    $item->collection_amount = $item->interest + $item->principal;
                    $item->payroll_id = optional($item->loan->loanRequest->employment)->payroll_id;
                    $item->employer = $employment->employer->name;
                    $item->collector = $collector->name ?? 'No Satff';
                });
                $collections = collect($collections->values());
                $info = "All Collections:  ";
                $info .= " Total Collections is ₦" . number_format($collections->sum('collection_amount'), 2);
                $info .= " Total Number of Collections is " . $collections->count();
            break;
            case 1:
                $collections = RepaymentPlan::paid()->whereBetween('payday', [$from, $to])
                ->with('loan.user')->get()
                ->filter(function($item){
                    return $item->loanActive();
                })
                ->each(function($item,$index){
                    $employer = optional($item->loan->loanRequest->employment)->employer;
                    //$staff = $item->loan->collector_type == 'AppModelsUser' ? 'no staff' : $item->loan->collector;
                    $item->collection_amount = $item->interest + $item->principal;
                    $item->payroll_id = optional($item->loan->loanRequest->employment)->payroll_id;
                    $item->employer = $employer->name;
                    //$item->collector = $staff->name;
                });
                $collections = collect($collections->values());
                $info = "All Paid Collections:  ";
                $info .= " Total Collections is ₦" . number_format($collections->sum('collection_amount'), 2);
                $info .= " Total Number of Collections is " . $collections->count();
            break;
            case 2:
                $collections = RepaymentPlan::unpaid()->whereBetween('payday', [$from, $to])
                ->with('loan.user')->get()
                ->filter(function($item){
                    return $item->loanActive();
                })
                ->each(function($item,$index){
                    $employer = optional($item->loan->loanRequest->employment)->employer;
                    //$staff = $item->loan->collector_type == 'AppModelsUser' ? 'no staff' : $item->loan->collector;
                    $item->collection_amount = $item->interest + $item->principal;
                    $item->payroll_id = optional($item->loan->loanRequest->employment)->payroll_id;
                    $item->employer = $employer->name;
                    //$item->collector = $staff->name;
                });
                $collections = collect($collections->values());
                $info = "All Unpaid Collections:  ";
                $info .= " Total Collections is ₦" . number_format($collections->sum('collection_amount'), 2);
                $info .= " Total Number of Collections is " . $collections->count();
            break;

        }
        

        return [$info, $collections];
    }

    public function InsuranceReport($request)
    {
        $from = $request->data['from'];
        $to = $request->data['to'];

        $employer_id = $request->data['employer'];
        

        if (!is_null($employer_id)) {
            $employer = Employer::find($employer_id);
            $activeLoans  = $employer->employeeLoansQuery()
                ->whereBetween('created_at', [$from, $to])
                ->get(['reference', 'amount', 'created_at', 'request_id', 'user_id'])
                ->each(function ($item, $index) {
                    $item->employer = $item->employer_name;
                    $item->insurance = $item->amount * (2.5 / 100);
                    $item->borrower = $item->user->name;
                });
            $info = "For  " . $employer->name . ":";
        }else{

            $activeLoans = Loan::whereBetween('created_at', [$from, $to])
            ->get(['reference', 'amount', 'request_id', 'created_at', 'user_id'])
            ->each(function ($item, $index) {
                $item->employer = $item->employer_name;
                $item->insurance = $item->amount * (2.5 / 100);
                $item->borrower = $item->user->name;
            });
            $info = "For all Employers:  ";
        }

        $info .= " the total amount of insurance paid from: " . $from . " to: " . $to . " is ₦" . number_format($activeLoans->sum('insurance'), 2);
        $info .= " total number of insurance is " . $activeLoans->count();

        return [$info, $activeLoans];
    }

    public function investorReport($request)
    {   
        
        $info = '';
        $reportHandler = new InvestorReportService($data = $request->data);

        $results = $reportHandler->getResult();
       
        $info .= " Total Amount  ₦" . number_format($results->sum('amount'), 2);
        $info .= " Total Number of Activities " . $results->count();

        return [$info, $results];
    }

    public function InvestmentReport($request)
    {

        $from = $request->data['from'];
        $to = $request->data['to'];

        $investor_id = $request->data['investor'];
       

        if (!is_null($investor_id)) {
            $investor = Investor::find($investor_id);
            $investments  = $investor->loanFundsQuery()
                ->whereBetween('created_at', [$from, $to])
                ->get(['id', 'reference', 'amount', 'created_at', 'request_id', 'investor_id', 'percentage'])
                ->each(function ($item, $index) {

                    $loanRequest = $item->loanRequest;

                    $item->loan = isset($loanRequest) ? $loanRequest->loan : null;
                    $item->investor_name = $item->investor->name;
                    $item->monthly_pay = isset($loanRequest) ? number_format($loanRequest->monthlyPayment($item->amount), 2) : null;
                });
            $info = "For  " . $investor->name . ":";
        }else{
            $investments = LoanFund::whereBetween('created_at', [$from, $to])
            ->get(['id', 'reference', 'amount', 'request_id', 'created_at', 'investor_id', 'percentage'])
            ->each(function ($item, $index) {

                $loanRequest = $item->loanRequest;

                $item->loan = isset($loanRequest) ? $loanRequest->loan : null;
                $item->investor_name = $item->investor->name;
                $item->monthly_pay = isset($loanRequest) ? number_format($loanRequest->monthlyPayment($item->amount), 2) : null;
            });

        $info = "For all Investors:  ";
        }

        $info .= " the total amount of investments made  from: " . $from . " to: " . $to . " is ₦" . number_format($investments->sum('amount'), 2);
        $info .= " total number of investments is " . $investments->count();

        return [$info, $investments];
    }

    public function FeesEarnedReport($request)
    {

        $from = $request->data['from'];
        $to = $request->data['to'];
        $employer_id = $request->data['employer'];
        
       

        if (!is_null($employer_id)) {

            $employer = Employer::find($employer_id);
            $loans  = $employer->getEmployeeLoans()->pluck('id')->toArray();
            $repayments = RepaymentPlan::whereBetween('created_at', [$from, $to])
                ->whereIn('loan_id', $loans)
                ->where('status', 1)
                ->with('loan:reference,id,request_id')
                ->get(['created_at', 'management_fee', 'loan_id'])
                ->each(function ($item, $index) {
                    $item->employer = $item->loan->employer_name;
                });
            $info = "For  " . $employer->name . ":";
        }else{
            $repayments = RepaymentPlan::whereBetween('created_at', [$from, $to])
            ->where('status', 1)
            ->with('loan:reference,id,request_id')
            ->get(['created_at', 'management_fee', 'loan_id'])
            ->each(function ($item, $index) {
                $item->employer = $item->loan->employer_name;
            });
        $info = "For all Employers:  ";
        }

        $info .= " the fees earned from: " . $from . " to: " . $to . " is ₦" . number_format($repayments->sum('management_fee'), 2);
        $info .= " total number of fees earned is " . $repayments->count();
        return [$info, $repayments];
    }

    public function CommissionsGivenReport($request)
    {
        $from = $request->data['from'];
        $to = $request->data['to'];
        $affiliate_id = $request->data['affiliate'];
       
        if (!is_null($affiliate_id)) {
            $affiliate = Affiliate::find($affiliate_id);
            $commission_ids = $affiliate->earnedCommissions()->pluck('id')->toArray();
            $commissions = WalletTransaction::whereBetween('created_at', [$from, $to])
                ->whereIn('id', $commission_ids)
                ->with('owner')
                ->with('entity')
                ->get(['id', 'owner_id', 'owner_type', 'amount', 'description', 'entity_id', 'entity_type', 'created_at'])->each(function ($item) {
                    $item->borrower = ($item->entity_type == 'App\Models\Loan') ? Loan::find($item->entity_id)->user->name : null;
                });

            $info = " For " . $affiliate->name . " :";
        }else{
            $commissions = WalletTransaction::whereBetween('created_at', [$from, $to])
            ->commissions()
            ->with('owner')
            ->with('entity')
            ->get(['id', 'owner_id', 'owner_type', 'amount', 'description', 'entity_id', 'entity_type', 'created_at'])->each(function ($item) {
                $item->borrower = ($item->entity_type == 'App\Models\Loan') ? Loan::find($item->entity_id)->user->name : null;
            });
        $info = " For all affiliates";

        }
        $info .= " commissions given from: " . $from . " to: " . $to . " is ₦" . number_format($commissions->sum('amount'), 2);
        $info .= " total number of commisions given is " . $commissions->count();
        return [$info, $commissions];
    }

    public function ActiveLoansReport($request)
    {

        $from = $request->data['from'];
        $to = $request->data['to'];


        $employer_id = $request->data['employer'];
       

        if (!is_null($employer_id)) {
            $employer = Employer::find($employer_id);
            $activeLoans  = $employer->employeeLoansQuery()
                ->whereBetween('created_at', [$from, $to])
                ->active()
                ->with('user')
                ->get(['id', 'reference', 'amount', 'created_at', 'request_id', 'user_id', 'due_date', 'duration', 'emi'])
                ->each(function ($item, $index) {
                    $item->employer = $item->employer_name;
                    $item->repayments_made = $item->closedPayments()->count();
                    $item->repayments_left = $item->unclosedPayments()->count();
                });
            $info = "For  " . $employer->name . ":";
        }else{
            $activeLoans = Loan::active()->whereBetween('created_at', [$from, $to])
            ->with('user')
            ->get(['id', 'reference', 'amount', 'created_at', 'request_id', 'user_id', 'due_date', 'duration', 'emi'])
            ->each(function ($item, $index) {

                $item->employer = $item->employer_name;
                $item->repayments_made = $item->closedPayments()->count();
                $item->repayments_left = $item->unclosedPayments()->count();
                $item->payroll_id = $item->user->employments->first()->payroll_id;
            });
        $info = "For all Employers:  ";
        }

        $info .= " the total active loans from: " . $from . " to: " . $to . " is ₦" . number_format($activeLoans->sum('amount'), 2);
        $info .= " total number of loans active is " . $activeLoans->count();

        return [$info, $activeLoans];
    }

    public function UserPortfolioReport(Request $request)
    {
       
        $from = $request->data['from'];
        $to = $request->data['to'];
        $employer_id = $request->data['employer'];   
        $days = $request->data['criteria'];  
        if (!is_null($employer_id)) {
            
            $employer = Employer::find($employer_id);
            $loans  = $employer->getEmployeeLoans()->pluck('id')->toArray();
            $confirmedPayments = RepaymentPlan::with('loan.user.employments')->whereStatus(0)->whereBetween('payday', [$from, $to])
            ->whereIn('loan_id', $loans)
            ->get(['paid_amount','loan_id', 'status', 'emi', 'management_fee', 'updated_at', 'payday', 'created_at'])->each(function($item, $index) {
                $item->amount = $item->paid_amount ? $item->paid_amount : $item->emi + $item->management_fee;
                if(isset($item->loan)) {
                    $item->repayments_made = $item->loan->closedPayments()->count();
                    $item->repayments_left = $item->loan->unclosedPayments()->count();
                    if($itemOwner = $item->loan->user){
                        if($itemOwner->employments->count() > 0) {
                            $item->payroll_id = $itemOwner->employments->first()->payroll_id;
                            $item->phone = $itemOwner->phone;                                        
                        }
                    }
                }
            })->filter(function($item) use ($request){
                $days = $request->data['criteria'];
                return $item->payday->diffInDays(now()) <=  $days;
            });
            

            $info = "For  " . $employer->name . ":";

            $info .= " The total unpaid emi from: " . $from . " to: " . $to . " is ₦" . number_format($confirmedPayments->sum('amount'), 2);
            $info .= " Total Number of Unpaid Plans is " . $confirmedPayments->count();         

            return [$info, $confirmedPayments];
        }else{
            
            $confirmedPayments = RepaymentPlan::with('loan.user.employments')->whereStatus(0)->whereBetween('payday', [$from, $to])           
            ->get(['paid_amount','loan_id', 'status', 'emi', 'management_fee', 'updated_at', 'payday', 'created_at'])->each(function($item, $index) {
                $item->amount = $item->paid_amount ? $item->paid_amount : $item->emi + $item->management_fee;                
                    if(isset($item->loan)) {
                        $item->repayments_made = $item->loan->closedPayments()->count();
                        $item->repayments_left = $item->loan->unclosedPayments()->count();
                        if($itemOwner = $item->loan->user){
                            if($itemOwner->employments->count() > 0) {
                                $item->payroll_id = $itemOwner->employments->first()->payroll_id;
                                $item->phone = $itemOwner->phone;                                        
                            }
                        }
                    }
                    
                                
            })->filter(function($item) use ($request){
                $days = $request->data['criteria'];
                return $item->payday->diffInDays(now()) <=  $days;
            });

            $info = " The total unpaid emi from: " . $from . " to: " . $to . " is ₦" . number_format($confirmedPayments->sum('amount'), 2);
            $info .= " Total Number of Plans Unpaid is " . $confirmedPayments->count();
            return [$info, $confirmedPayments];
        }
       
    }

    public function PenaltyReport($request)
    { }


   
}
