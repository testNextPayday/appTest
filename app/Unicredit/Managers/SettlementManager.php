<?php

/**
 *  SettlementManager class 
 */

namespace App\Unicredit\Managers;

use PDF;
use App\Models\Loan;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Unicodeveloper\Paystack\Paystack;
use App\Services\LoanRepaymentService;
use App\Unicredit\Contracts\PaymentGateway;
use App\Notifications\Users\SettlementCreated;


/**
 * The Settlement Manager gives us ability to settle
 */
class SettlementManager
{
    
    protected $paymentGateway;

    public $repaymentService;
    /**
     * The constructor sets paystack as default payment channel
     * We can overide this in our testing or another class
     * By using the setPaymentHandler
     *
     * @return void
     */
    public function __construct()
    {
        $this->setPaymentHandler(new Paystack);
        $this->setRepaymentService(new LoanRepaymentService);
    
    }
    
    /**
     * Sets the handler for the settlement 
     * Using Method Dependency injection
     *
     * @param  mixed $paymentHandler
     * @return void
     */
    public function setPaymentHandler($paymentHandler)
    {
        $this->paymentGateway = $paymentHandler;
    }

    public function setRepaymentService($repaymentService)
    {
        $this->repaymentService  = $repaymentService;
    }

    
    /**
     * Takes us to the payment gateway payment page
     *
     * @param  mixed $request
     * @param  mixed $callbackUrl
     * @return void
     */
    public function makePayment($request,$callbackUrl)
    {
        if ($this->loanSettled($request)) {

            return redirect()->back()->with(
                'failure', 'This Loan has applied for settlement'
            );
        }
        
        $request->request->add(['callback_url' => $callbackUrl]);

        $amountInNaira = $request->amount / 100;

        $amount = (($amountInNaira) + paystack_charge($amountInNaira)) * 100;

        $request->request->add(['amount'=>$amount]);
        return $this->paymentGateway->getAuthorizationUrl()->redirectNow();
    }

    
    /**
     * Handles each models redirections with their urls
     *
     * @param  mixed $previousUrlString
     * @param  mixed $loanUrlString
     * @return void
     */
    public function handlePaymentCallback($previousUrlString,$loanUrlString)
    {
        $paystackData = $this->paymentGateway->getPaymentData();
      
        $reference = $paystackData['data']['metadata']['loan_reference'];
        $amount = ($paystackData['data']['amount'])/100;
        if (! $paystackData['data']['status'] == 'success') {
            return redirect()->route($previousUrlString, ['reference'=>$reference])->with('failure', 'Your transaction was not successful');
        }
         $loan = Loan::whereReference($reference)->first();
        $data = [
            'loan_id'=>$loan->id,
            'amount'=>$amount,
            'status'=>1,
            'status_message'=>' Processing',
            'paid_at'=>now(),
            'collection_method'=>'Paystack',
            'investors_cut'=>$loan->calculateInvestorsCut(),
        ];
        try {
            $settlement = Settlement::create($data);
            $this->createInvoice($settlement, $loan);

            $this->repaymentService->makeSettlementUpload($settlement);
           
           //$loan->user->notify(new SettlementCreated($settlement));
        }catch(\Exception | QueryException $e){
            $err_msg = isset($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage();
           
            return redirect()->route($loanUrlString,['reference'=>$reference])->with('failure',$err_msg);
        }
      

        return redirect()->route($loanUrlString,['reference'=>$reference])->with('success','Settlement Application was successful');
    }
    
    /**
     * Checks to see if a loan has been settled
     *
     * @param  mixed $request
     * @return void
     */
    public function loanSettled(Request $request)
    {
        $loanRef = json_decode($request->metadata);
        $loan = Loan::whereReference($loanRef->loan_reference)->first();
        return isset($loan->settlement);

    }

    
    /**
     * Generates an invoice for the user
     *
     * @param  mixed $settlement
     * @param  mixed $loan
     * @return void
     */
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


}