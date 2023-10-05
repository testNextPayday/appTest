<?php
namespace App\Traits\Managers;
use App\Models\Loan;
use App\Models\Settlement;
use App\Notifications\Users\SettlementCreated;
use Illuminate\Database\QueryException;
use Paystack;
use Illuminate\Http\Request;


trait  SettlementManager 
{

    public function makePayment($request,$callbackUrl)
    {
        
        if ($this->loanSettled($request)) {

            return redirect()->back()->with(
                'failure', 'This Loan has applied for settlement'
            );
        }
       
        $request->request->add(['callback_url' => $callbackUrl]);
        //adding charge to amount to be paid

        $amount = $request->amount + paystack_charge($request->amount);

        $request->request->add(['amount'=>$amount]);

        return Paystack::getAuthorizationUrl()->redirectNow();
    }


    public function handlePaymentCallback($previousUrlString,$loanUrlString)
    {
        $paystackData = Paystack::getPaymentData();
      
        $reference = $paystackData['data']['metadata']['loan_reference'];
        $amount = ($paystackData['data']['amount'])/100;
        if(! $paystackData['status']) return redirect()->route($previousUrlString,['reference'=>$reference])->with('failure',' Your transaction was not successful');
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

            $this->createInvoice($settlement,$loan);

           $loan->user->notify(new SettlementCreated($settlement));

        }catch(\Exception | QueryException $e){
            $err_msg = isset($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage();
            return redirect()->route($loanUrlString,['reference'=>$reference])->with('failure',$err_msg);
        }
      

        return redirect()->route($loanUrlString,['reference'=>$reference])->with('success','Settlement Application was successful');
    }

    public function loanSettled(Request $request)
    {
        $loanRef = json_decode($request->metadata);
        $loan = Loan::whereReference($loanRef->loan_reference)->first();
        return isset($loan->settlement);

    }
}

?>