<?php

namespace App\Http\Controllers\Staff;

use PDF;
use Exception;
use Carbon\Carbon;
use App\Models\Loan;
use App\LoanTransaction;
use App\Helpers\Constants;
use App\Models\Settlement;
use Illuminate\Http\Request;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use App\Remita\DDM\MandateManager;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TotalPenaltiesController;
use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Support\Facades\Storage;
use App\Services\Penalty\PenaltyService;
use App\Services\LoanRequestUpgradeService;
use App\Unicredit\Managers\SettlementManager;
use App\Unicredit\Collection\RepaymentManager;
use App\Notifications\Users\SettlementCreated;
use App\Notifications\Users\SettlementApproved;
use App\Notifications\Users\SettlementDeclined;
use App\Services\Penalty\PenaltyTakeOutService;
use App\Notifications\Investors\LoanSettledNotification;

class SettlementController extends Controller
{

    protected $upgradeService;

    public function __construct(LoanRequestUpgradeService $loanRequestUpgradeService)
    {
        // $this->middleware('admin');
        $this->upgradeService = $loanRequestUpgradeService;
    }
    
    public function index()
    {
        $settlements = Settlement::all();
        return view('staff.settlement.index', ['settlements' => $settlements]);
    }

    public function pay(Request $request, SettlementManager $manager)
    {
        $callbackUrl  = route('staff.settlement.payment_callback');
        return $manager->makePayment($request, $callbackUrl);
    }

    public function handleSettlementPaymentCallback(SettlementManager $manager)
    {
        $previousUrlString = 'staff.loans.view';
        $loanUrlString = 'staff.loans.view';
        return $manager->handlePaymentCallback($previousUrlString, $loanUrlString);

    }


    public function new()
    {
        return view('staff.settlement.new');
    }


    public function uploadSettlement(Request $request, SettlementManager $manager)
    {

       
        $loan = Loan::whereReference($request->reference)->first();

        if (!$loan) return redirect()->back()->with('failure', ' Loan with such reference was not found');

        $verify = $this->verifySettlement($loan);

        if (!$verify['status']) return redirect()->back()->with('failure', $verify['message']);

        $filename = null;

        if ($request->hasFile('payment_proof') && $request->file('payment_proof')->isValid()) {
           
            $filename = Storage::disk('public')->putFile('settlement_proofs', $request->file('payment_proof'));
        }else{
            return redirect()->back()->with('failure',' Payment proof is compulsory');
        }
       
      
        try {
            DB::beginTransaction();
                $settlement = Settlement::create([
                    'loan_id' => $loan->id,
                    'collection_method' => $request->method,
                    'amount' => $request->amount,
                    'paid_at' => $request->paid_at,
                    'payment_proof' => $filename,
                    'status' => 1,
                    'investors_cut'=>$loan->calculateInvestorsCut(),
                ]);
                $this->createInvoice($settlement, $loan);
                $manager->repaymentService->makeSettlementUpload($settlement);
                //$loan->user->notify(new SettlementCreated($settlement));
            DB::commit();
            return redirect()->back()->with('success', ' Settlement uploaded');
        } catch (\Exception $e) {
            DB::rollBack();
            
                return redirect()->back()->with('failure', $e->getMessage());
            
            return redirect()->back()->with('failure', 'An error occurred. Try again with right details');
        }
        
    }

    public function createInvoice($settlement, $loan)
    {
        $pdf_name = 'Loan-Settlement-' . $loan->reference . '.pdf';
        $pdf = PDF::loadView('pdfs.loan_settlement', [
            'settlement' => $settlement
        ]);
        set_time_limit(200);

        $pdf_link = public_path() . '/storage/pdfs/loan_settlements/' . $pdf_name;
       
        $settlement->update(['invoice'=>$pdf_link]);
        $pdf->save($pdf_link);
        return;
    }

    public function previewDoc(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
        $pdf_name = 'Loan-' . $loan->reference . '.pdf';
        $pdf = PDF::loadView('pdfs.settlement_preview', [
            'loan' => $loan,
            'latePaymentCharges' => TotalPenaltiesController::total($loan)
        ]);
        set_time_limit(200);

        $pdf_link = public_path() . '/storage/pdfs/loan_settlements/' . $pdf_name;
        $pdf->save($pdf_link);
        return response()->file($pdf_link);

    }


    public function verifySettlement($loan)
    {
        if (!$loan->canSettle()) return array('status' => false, 'message' => ' This loan does not meet the requirements to settle');
        if ($loan->settlement && $loan->settlement->status != 3) return array('status' => false, 'message' => ' This loan already has a settlement');
        return array('status' => true);
    }


    public function view(Request $request)
    {
        $settlement = Settlement::whereReference($request->reference)->first();
        return view('staff.settlement.show',['settlement'=>$settlement]);
    }
    public function confirm(Request $request, SettlementManager $manager)
    {
        $settlement = Settlement::whereReference($request->reference)->first();
        $loan = $settlement->loan;
        $user = $settlement->loan->user;
      
        try {

            DB::beginTransaction();

            $settlement->update(['status' => 2,'status_message'=>' Confirmed']);
            
            //$user->notify(new SettlementApproved($settlement));
            // update loan to fulfilled
            $loan->update(['status' => '2']);
            $this->updateUserWallet($loan);
            //$this->notifyInvestors($loan);
            // send investor notification
            if($settlement->invoice) Storage::delete($settlement->invoice);
            $this->createInvoice($settlement, $loan);
            //(new RepaymentManager())->payOffInvestorOnSettlement($loan);
            
             // update all repayments
            $manager->repaymentService->makeSettlementApproval($settlement);

            if ($loan->is_penalized) {

                (new PenaltyService())->takeOutPenalty($loan);
            }

            if(!$loan->is_penalized) {
                
                if($this->upgradeService->checkLoanRequiresUpgrade($loan)){
                    $this->upgradeService->upgradeUser($loan);
                }                
            }
            // stop mandate on loan
            $mandateManager = new MandateManager();

            $dbLogger = new DatabaseLogger();

            if(isset($loan->mandateId)){

                $response = $mandateManager->stopMandate($loan);
    
                $dbLogger->log($loan,$response,'mandate-stop');
    
                if ($response->isASuccess()) {
                    // mandate is incactive 
                    $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 1);
                }
    
                session()->flash('info','Loan Mandate Stopage Sent');
            }
            $loan->user->update(['loan_wallet'=> 0.0]);
            DB::commit();
            return redirect()->back()->with('success', ' Settlement has successfully been confirmed');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('failure', $e->getMessage());
        }
        DB::commit();
        return redirect()->back()->with('success', ' Settlement has successfully been confirmed');
    }

    public function notifyInvestors($loan)
    {
        $funds = $loan->loanRequest
        ->funds()
        ->where('is_current', true)
        ->whereIn('status', [2, 4])
        ->get();

        foreach($funds as $fund){
            //$fund->investor->notify(new LoanSettledNotification($loan));
        }
    }


    public function updateUserWallet($loan)
    {
        $amount = $loan->repaymentPlans->last()->wallet_balance;
        $user = $loan->user;
        if($amount > 0){
            if($amount > 0){
                $this->addTransaction($loan,$amount);
                $this->logTransaction($user,$amount,$loan);
                $new_wallet_balance = $user->wallet + $amount;
                $user->update(['wallet'=>$new_wallet_balance]);
            }
            
        }
        $user->update(['loan_wallet'=>0]); // We now reset the loan wallet to 0
    }


    public function logTransaction($user,$amount,$loan)
    {
        $financer  = new FinanceHandler(new TransactionLogger);
        $code = config('unicredit.flow')['repayment_wallet_recovery'];
        $financer->handleSingle($user,'credit',$amount,$loan,'W',$code);
        return true;
    }

    public function addTransaction($loan,$amount)
    {
        return LoanTransaction::create([
            'name'=> ' Moving repayment balance to user wallet',
            'type'=>' Debit',
            'loan_id'=>$loan->id,
            'description'=> ' Moving repayment balance to wallet',
            'amount'=>$amount
        ]);
    }

    public function settlementInvoice(Request $request)
    {
        $settlement =  Settlement::whereReference($request->reference)->first();
        if(! $settlement) return redirect()->back()->with('failure',' No invoice found for this settlement');
        return response()->file($settlement->invoice);
    }

    public function unconfirm(Request $request)
    { }

    public function decline(Request $request)
    {

        try{
            DB::beginTransaction();
            $settlement = Settlement::whereReference($request->reference)->first();
            $settlement->update(['status' => 3, 'status_message' => $request->status_message]);
            $this->createInvoice($settlement, $settlement->loan);
            $user = $settlement->loan->user;
            //$user->notify(new SettlementDeclined($settlement));

        }catch(\Exception $e){
            DB::rollBack();
            
            return redirect()->back()->with('failure', $e->getMessage());
        }
        DB::commit();
       
        return redirect()->back()->with('success', ' The settlement has been declined');
    }



}
