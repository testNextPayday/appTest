<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\RecovaCollections;
use App\Models\User;
use App\Models\LoanFund;
use App\Traits\LoanPenaltyMethods;
use Illuminate\Http\Request;
use App\Traits\EncryptDecrypt;
use App\Models\LoanTransaction;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TotalPenaltiesController;
use App\Models\LoanRequest;
use App\Models\LoanWalletTransaction;
use App\Models\PenaltySetting;
use App\Models\RepaymentPlan;
use App\Traits\Managers\LoanManager;
use App\Services\DissolveLoanService;

use App\Unicredit\Utils\FulfillingService;
use App\Services\AutoCollectionSetupService;
use App\Unicredit\Contracts\Models\ILoanRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    use EncryptDecrypt,LoanManager;


    public function __construct(ILoanRepository $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }
    
    public function updateSweepParams(Request $request, Loan $loan)
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
        
        if ($loan->update($request->only(array_keys($rules)))) {
            return redirect()->back()->with('success', 'Sweep parameters updated successfully');
        }
        
        return redirect()->back()
            ->with('failure', 'Sweep parameters could not be updated. Please try again');
    }

    public function loanBalanceUpdate( Request $request)
    {
    
        $val =  Validator::make($request->all(), [

        'loanReference' => 'required|string',
        'debitedAmount' => 'required',
        'recoveryFee' => 'required',

        'settlementAmount' => 'required',
        'transactionReference' => 'required|string',
        'narration' => 'required|string',
        'institutionCode' => 'required|string',
        
        ]);

        if ($val->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $val->errors()
            ]);
        }

        RecovaCollections::create([
            'loan_reference' => $request->loanReference,
            'debited_amount' => $request->debitedAmount,
            'recovery_fee' => $request->recoveryFee,
    
            'settlement_amount' => $request->settlementAmount,
            'transaction_reference' => $request->transactionReference,
            'narration' => $request->narration,
            'institution_code' => $request->institutionCode,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Loan balance updated successfully',
        ]);


    }
    
    
    public function creditHistory($bvn) {
        $user = User::where('bvn', $bvn)->firstOrFail();

        $loans = Loan::with('user', 'repaymentPlans')->where('user_id', $user->id)
            ->where('status', '1')
            // ->where('is_penalized', 0)
            ->get();

        return response()->json([
           'customerId' => $user->id,
           'openedLoanAccounts' => Loan::where('user_id', $user->id)->where('status', '1')->count(),
           'closedLoanAccounts' => Loan::where('user_id', $user->id)->where('status', '2')->count(),
            'PerformingLoanAccounts' => Loan::where('user_id', $user->id)->where('status', '1')->count(),
            'nonPerformingLoanAccounts' => Loan::where('user_id', $user->id)->where('status', '3')->count(),
            'LoansWrittenOff' => Loan::where('user_id', $user->id)->count(),
            'loansWrittenOffAmount' => Loan::where('user_id', $user->id)->sum('amount'),
            'details' => Loan::where('user_id', $user->id)->get()
        
        
        ]);
    }

    public function viewLoan($reference)
    {



        $loan = Loan::whereReference($reference)->with('user', 'repaymentPlans')->first();

        // dd(LoanPenaltyMethods::class->getWalletTransactionsForPenaltySum());
        // return LoanWalletTransactions::whereIsPenalty(true)->sum('amount');


       $excessBalance = TotalPenaltiesController::total($loan);

       $excesspenalties = [0];
       $lastP = abs($loan->user->masked_loan_wallet); 
      
        if(!$loan)
            $loan =  Loan::withTrashed()->wherereference($reference)->first();
        /////////////////////////////Penalty that has passed loan duration ///////////////////////////////////////////////////////////////////

        
       
        if ($loan->is_penalized == 1) {

           
            $durationAfterExpiration = Carbon::parse($loan->created_at)->diffInMonths(now())  - $loan->duration;
    
            $loan_request = LoanRequest::where('id', $loan->request_id)->latest()->first();
    
            if(PenaltySetting::where('entity_id', $loan_request->employment)->exists()){
                $penalty = PenaltySetting::where('entity_id', $loan_request->employment->employer->id)
                                ->first();

                                $loanPenalty = PenaltySetting::where('entity_id', $loan->id);
            if ($loanPenalty->exists()) {
                $penalty = $loanPenalty->latest()->first();
                $penaltyPercent = $loanPenalty->latest()->first()->value/100;
            }else{
               
                $penaltyPercent = 0;
            }

            if ($penalty->excess_penalty_status == 1) {
                # code...
                while($durationAfterExpiration > 0){
                    $currentPenalty = $lastP + ($lastP * $penaltyPercent);
                    $lastP = $currentPenalty;
                    $durationAfterExpiration--;
                    array_push($excesspenalties, $lastP);        
                }
            }
            }
            
            // dd($penalty);
            

            

    
        }
        

        if (end($excesspenalties) > 0) {
            $maturity_penalty = '-'.end($excesspenalties); 
        }else{
            $maturity_penalty = $loan->user->masked_loan_wallet; 
        }

        // dd($maturity_penalty);




        $previousdebits = RecovaCollections::where('loan_reference', $reference)->sum('debited_amount');
        $recoveryFee = RecovaCollections::where('loan_reference', $reference)->sum('recovery_fee');
        // return response()->json([
        //     $previousdebits
        // ]);

        

        

        
        //////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////

        $dueAmount = abs($maturity_penalty) + $recoveryFee - $previousdebits;
        
        if(!$loan){
            return response()->json([
                'status' => 'failed',
                'message' => 'Loan does not exist'
            ]);
        
        
            
        }else{
            return response()->json([

                // 'loan', 
                // 'excessBalance', 
                // 'excesspenalties', 
                'loanReference' => $reference,
                'amountDue' => $dueAmount,
        ]);
        }
            
    }
    public function view($reference)
    {

        // dd(LoanPenaltyMethods::class->getWalletTransactionsForPenaltySum());
        // dd(new LoanPenaltyMethods);

        $loan = Loan::whereReference($reference)->with('user', 'repaymentPlans')->first();

        // dd(LoanPenaltyMethods::class->getWalletTransactionsForPenaltySum());
        // return LoanWalletTransactions::whereIsPenalty(true)->sum('amount');


       $excessBalance = TotalPenaltiesController::total($loan);

       $excesspenalties = [0];
       $lastP = abs($loan->user->masked_loan_wallet); 
      
        if(!$loan)
            $loan =  Loan::withTrashed()->wherereference($reference)->first();
        /////////////////////////////Penalty that has passed loan duration ///////////////////////////////////////////////////////////////////

        
       
        if ($loan->is_penalized == 1) {

           
            $durationAfterExpiration = Carbon::parse($loan->created_at)->diffInMonths(now())  - $loan->duration;
    
            $loan_request = LoanRequest::where('id', $loan->request_id)->latest()->first();
    
            $penalty = PenaltySetting::where('entity_id', $loan_request->employment->employer->id)
                                ->first();

            $loanPenalty = PenaltySetting::where('entity_id', $loan->id);
            if ($loanPenalty->exists()) {
                $penalty = $loanPenalty->latest()->first();
                $penaltyPercent = $loanPenalty->latest()->first()->value/100;
            }else{
               
                $penaltyPercent = $penalty->value/100;
            }

            if ($penalty->excess_penalty_status == 1) {
                # code...
                while($durationAfterExpiration > 0){
                    $currentPenalty = $lastP + ($lastP * $penaltyPercent);
                    $lastP = $currentPenalty;
                    $durationAfterExpiration--;
                    array_push($excesspenalties, $lastP);        
                }
            }

    
        }

        $lastElement = end($excesspenalties);
        if ($lastElement > 0) {
            $maturity_penalty = '-' . $lastElement;
        } else {
            $maturity_penalty = $loan->user->masked_loan_wallet;
        }

        // dd($maturity_penalty);


        

        

        
        //////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////
        
        if(!$loan)
            return redirect()->back()->with('failure', 'Loan does not exist');
        return view('admin.loans.show', compact('loan', 'excessBalance', 'excesspenalties', 'maturity_penalty'));
    }

    public function backDateCollectionDate(Request $request)
    {
        $loan = Loan::findOrFail($request->loan_id);
        $loan->created_at = Carbon::parse($request->new_date);
        $loan->save();

        return redirect()->back()->with('success', 'Collection date successfully updated');
        
    }


    public function backDateDueDate(Request $request)
    {
        $loan = Loan::findOrFail($request->loan_id);
        $loan->due_date = Carbon::parse($request->new_date);
        $loan->save();

        $getNewDay = Carbon::parse($request->new_date)->format('d');
        
        $repaymentPlans = RepaymentPlan::where('loan_id', $request->loan_id)->get();

        foreach ($repaymentPlans as $repay) {
            $explodedDay = explode('-', $repay->payday);
            $newRepayday = Carbon::parse($explodedDay[0] . '-' . $explodedDay[1] . '-' . $getNewDay);
            $repay->payday = $newRepayday;
            $repay->save();
        }

        return redirect()->back()->with('success', 'Due date successfully updated');
        
    }
    
    public function backDatePayDays($loan_id, $type){
     
        $repaymentPlan = RepaymentPlan::where('loan_id', $loan_id);
        $repaymentPlans = $repaymentPlan->get();
   
        if ($type == 'backdate') {
            # code...
            foreach ($repaymentPlans as $repay) {
                $repay->payday = Carbon::parse($repay->payday)->subMonth(); //$newRepayday;
                $repay->save();
            }
        }else{
            foreach ($repaymentPlans as $repay) {
                $repay->payday = Carbon::parse($repay->payday)->addMonth(); //$newRepayday;
                $repay->save();
            }
        }

        return redirect()->back()->with('success', 'Due date successfully updated');
        
    }

    public function changeSingleDueDate(Request $request){

        $request->validate([
            'plan_id' => 'required',
            'new_date' => 'required'
        ]);


        $getNewDay = Carbon::parse($request->new_date);
        
        $repaymentPlans = RepaymentPlan::findOrFail($request->plan_id);
        $repaymentPlans->payday = $getNewDay;
        $repaymentPlans->save();

        return back()->with('success', 'Loann repayment due date changed successfully');    }

    public function settleEMI(Request $request)
    {
        $loan = Loan::findOrFail($request->loan_id);
        // get repayment were is not paid and settle it from user wallet
        // to be impletemented after confirmation from Mr. Thod

    }
    
    
    public function pendingLoans()
    {
        $loans = Loan::where('status', "0")->latest()->get();
        return view('admin.loans.pending', compact('loans'));
    }


    public function activatedAutoSweep()
    {
        $loans = $this->loanRepository->getActiveStatusLoans()->where('auto_sweeping', true);
        $label = 'Active Auto';
        return view('admin.loans.active-sweep', compact('loans', 'label'));
    }

    public function remitaActivatedSweep()
    {
        $loans = $this->loanRepository->getActiveStatusLoans()->where('remita_active', true);
        $label = 'Remita Active';
        return view('admin.loans.active-sweep', compact('loans', 'label'));
    }

    
    /**
     * Get all loans that has been restructured
     *
     * @return void
     */
    public function restructuredLoans()
    {
        $loans = $this->loanRepository->getActiveStatusLoans()->where('is_restructured', 1);

        return view('admin.loans.restructured', compact('loans'));
    }

    
    /**
     * Toggles disable sweep
     *
     * @param  mixed $loan
     * @return void
     */
    public function toggleSweepPause(Loan $loan, AutoCollectionSetupService $autoService) {

        try {

            $autoService->toggleManualSweep($loan);

        }catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Loan sweep status updated');
    }

    /**
     * Toggles disable sweep
     *
     * @param  mixed $loan
     * @return void
     */
    public function toggleAutoSweep(Loan $loan, AutoCollectionSetupService $autoService) {

        try {

            $autoService->toggleAutoSweep($loan);

        }catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Loan sweep status updated');
    }

    /**
     * Toggles disable sweep
     *
     * @param  mixed $loan
     * @return void
     */
    public function toggleAutoRemita(Loan $loan, AutoCollectionSetupService $autoService) {

        try {

            $autoService->toggleAutoRemita($loan);

        }catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Loan sweep status updated');
    }

    public function fulfilledLoans(Request $request){
        
        
        // if ($request->isMethod('post')) {

        //     $loans = Loan::whereStatus('2')->whereBetween(
        //         'created_at', [$request->from, $request->to])->with(['user.employments'])->get();

        // } else{

        //     $loans = Loan::whereStatus('2')->with(['user.employments'])->get();
        // }
        $loans = $this->loanRepository->getFulfilledLoans();
        
        return view('admin.loans.fulfilled', compact('loans'));
    }

    public function activeLoans()
    {
        $loans = $this->loanRepository->getActiveStatusLoans()->where('is_managed', false);
        return view('admin.loans.active', compact('loans'));
    }
    
    /**
     * Dissolves a loan 
     *
     * @param  mixed $request
     * @param  mixed $loan
     * @return void
     */
    public function dissolveLoan(Request $request, DissolveLoanService $dissolveService)
    {
        try {
            
            DB::beginTransaction();
                $loan = Loan::whereReference($request->reference)->first();
                if(! $loan) {
                    throw new \InvalidArgumentException('Loan not found');
                }
                $dissolveService->dissolveLoan($loan);
            DB::commit();

            return redirect()->route('admin.loans.pending')->with('success', 'Loan has been dissolved successfully');

        }catch(\Exception $e) {

            DB::rollback();

            return redirect()->back()->with('failure', $e->getMessage());
        }
    }

    // this are loans that are deleted from the system
    public function voidLoans()
    {
        $loans = Loan::onlyTrashed()->with(['user.employments'])->latest()->get();
        return view('admin.loans.void', compact('loans'));
    }

    public function inActiveLoans()
    {
        $loans = $this->loanRepository->getInActiveLoans();
        return view('admin.loans.inactive',compact('loans'));
    }

    public function penalizedLoans()
    {
        $loans = $this->loanRepository->getActiveStatusLoans()->where('is_penalized', true);
        return view('admin.loans.penalized', compact('loans'));
    }

    //groups an active loan as managed
    public function managedLoans()
    {
       
        $loans = $this->loanRepository->getActiveStatusLoans()->where('is_managed', true);
        return view('admin.loans.managed', compact('loans'));
    }

    // This sets a loan aside as managed loan
    public function markAsManaged(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
        if(!$loan) return redirect()->back()->with('failure',' Loan with such reference was not found');

        $loan->update(['is_managed'=>true]);
        return redirect()->back()->with('success','Loan has been moved to managed');
    }

     
    /**
     * markAsVoid
     *  this action deletes a loan
     * @param  mixed $request
     *
     * @return void
     */
    public function markAsVoid(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
        if(!$loan) return redirect()->back()->with('failure',' Loan with such reference was not found');
        $loan->delete();
        return redirect()->back()->with('success','Loan has been moved to void');
    }

    
    /**
     * restoreVoid
     *  This action restores a deleted loan
     * @param  mixed $request
     *
     * @return void
     */
    public function restoreVoid(Request $request)
    {
        $loan = Loan::onlyTrashed()->whereReference($request->reference)->first();
        if(!$loan) return redirect()->back()->with('failure',' Loan with such reference was not found');
        $loan->restore();
        return redirect()->back()->with('success','Loan has been restored');
    }

  
    /**
     * markAsActive
     *    brings back a managed loan by updating the is_managed column to false
     * @param  mixed $request
     *
     * @return void
     */
    public function markAsActive(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
        if(!$loan) return redirect()->back()->with('failure',' Loan with such reference was not found');

        $loan->update(['is_managed'=>false,'status'=>'1']);
        return redirect()->back()->with('success','Loan has been moved to active');
    }


    /**
     * markAsInactive
     *  Mark a voided loan or active loan as inactive
     * @param  mixed $request
     *
     * @return void
     */
    public function markAsInactive(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first() ?? Loan::onlyTrashed()->whereReference($request->reference)->first();
        
        if(!$loan) return redirect()->back()->with('failure',' Loan with such reference was not found');
        $loan->trashed() ? $loan->restore() : true ;
        $loan->update(['is_managed'=>false,'status'=>'3']);
        return redirect()->back()->with('success','Loan has been moved to inactive');
    }

    

    public function eligibleTopUp(){
        $loans = $this->loanRepository->getEligibleTopup();
        return view('admin.loans.top_up', compact('loans'));
    }


    public function sweepableLoans()
    {
        $loans = $this->getSweepableLoans();
        return view('admin.loans.sweepable',compact('loans'));
    }
    
    public function receivedLoans()
    {
        $loans = Loan::latest()->paginate(20);
        return view('admin.loans_received', compact('loans'));
    }

    public function givenLoans()
    {
        $loans = LoanFund::latest()->paginate(20);
        return view('admin.loans_given', compact('loans'));
    }
    public function viewGivenLoan($loan_id)
    {
        $loan_id = $this->basicDecrypt($loan_id);  
        $loan = LoanFund::find($loan_id);
        if(!$loan) return redirect()->back()->with('failure', 'Loan not found');
        $fundFraction = 0;
        $repaymentPlans = [];
        $loaneeLoan = null;
        $currentValue = $loan->amount;
        if($loan->status > 1) {
            $loaneeLoan = $loan->loanRequest->loan;
            $totalCollectedByLoanee = $loaneeLoan->amount;
            $fundFraction = $loan->amount/$totalCollectedByLoanee;
            $repaymentPlans = $loaneeLoan->repaymentPlans;
            $currentValue = $fundFraction * ($loaneeLoan->repaymentPlans()->whereStatus(false)->sum('interest') + 
            $loaneeLoan->repaymentPlans()->whereStatus(false)->sum('principal'));
        }
        return view('admin.loan_fund', compact('loan', 'fundFraction', 'repaymentPlans', 'loaneeLoan', 'currentValue'));
    }
    
    public function acquiredLoans()
    {
        $loans = $this->loanRepository->getAcquiredFunds();
        return view('admin.loans_acquired', compact('loans'));
    }
    
    public function viewAcquiredLoan($loan_id)
    {
        $loan_id = $this->basicDecrypt($loan_id);  
        $loan = LoanFund::find($loan_id);
        if(!$loan) return redirect()->back()->with('failure', 'Loan not found');
        $original_loan = $loan->original;
        while($original_loan->original_id != null) {
            $origianl_loan = $origianl_loan->original;
        }
        $loaneeLoan = $original_loan->loanRequest->loan;
        $totalCollectedByLoanee = $loaneeLoan->amount;
        $fundFraction = $original_loan->amount/$totalCollectedByLoanee;
        $repaymentPlans = $loaneeLoan->repaymentPlans;
        $currentValue = $fundFraction * ($loaneeLoan->repaymentPlans()->whereStatus(false)->sum('interest') + 
        $loaneeLoan->repaymentPlans()->whereStatus(false)->sum('principal'));
    
        return view('admin.loan_acquired', compact('loan', 'original_loan', 'fundFraction', 'repaymentPlans', 'loaneeLoan', 'currentValue'));
    }
    
    public function getAvailableLoanPurchases() 
    {
        $loans = LoanFund::latest()->whereStatus(4)->paginate(25);
        return view('admin.loan_purchases_available', compact('loans'));
    }

    public function pushCardCollectionToDas(Request $request, Loan $loan)
    {
        try {

           

        }catch (\Exception $e) {

            return $this->sendExceptionResponse($e);
        }

        return redirect()->back()->with('success', 'Loan pushed to DAS');
    }

    public function fulfillLoan($reference){
        $service = new FulfillingService();
        $loan = Loan::whereReference($reference)->first();
        if($service->fulfill($loan)){
            return redirect()->back()->with('success', 'Loan Fulfilled Successfully');           
        }
        return redirect()->back()->with('failure', 'Loan Fulfillment Failed, Please Try Again'); 
    }
    
    public function unfulfillLoan($reference){
        $loan = Loan::whereReference($reference)->first();        
        $unfulfill = $loan->update(['status' => '1' ]);
        if($unfulfill){
            return redirect()->back()->with('success', 'Loan Unfulfilled');            
        }return redirect()->back()->with('failure', 'Loan cannot be Unfulfilled, Please Try Again');
        
    }

    public function zeroriseWallet($reference){
        $user = User::whereReference($reference)->first();
        if(!$user) return redirect()->back()->with('failure',' User with such reference was not found');
        
        $user->update(['loan_wallet'=>0.00]);
        return redirect()->back()->with('success','User Loan Wallet has been equated to ₦0.00');        
    }
    
}