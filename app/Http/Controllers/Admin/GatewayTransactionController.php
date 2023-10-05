<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\GatewayTransaction;
use App\Traits\ChecksPaymentStatus;
use App\Http\Controllers\Controller;
use App\Unicredit\Contracts\MoneySender;
use App\Unicredit\Payments\NextPayClient;
use App\Http\Resources\TransactionResource;
use App\Unicredit\Payments\NextPayClientAdapter;

class GatewayTransactionController extends Controller
{
    //
    use ChecksPaymentStatus;

    public function __construct(MoneySender $channel)
    {

        $this->channel = $channel;
    }


    public function index()
    {
        return view('admin.payments.transactions');
    }



    public function getRecords(Request $request)
    {
        
        $searchBy = strtolower($request->query('searchBy'));

        $searchStatus = strtolower($request->query('searchStatus'));

        $endDate = Carbon::parse($request->query('endDate'))
        ->addDays(1)->toDateString();
       

        $startDate = Carbon::parse($request->query('startDate'))
        ->subDays(1)->toDateString();
    
        $transactions = GatewayTransaction::whereBetween(
            'created_at', [$startDate, $endDate]
        )->with(['owner','link'])->latest()->get();

        $transactions = TransactionResource::collection($transactions);
       

        if ($searchBy != strtolower('All')) {

           $transactions = $transactions->filter( 
               function($value) use ($searchBy) {
                
                $str_pos = strpos(strtolower(str_replace('\\', '', $value->link_type)), $searchBy);

                return $str_pos != false;

              }
            );

        }

        if($searchStatus != strtolower('All')){

            $transactions = $transactions->filter(function($value) use ($searchStatus){

                return $value->status_message == $searchStatus;
            });
        }


        $transactions  = !is_array($transactions) ? $transactions->values()->toArray() : $transactions;
        
        return response()->json($transactions);
    }


   

    public function retryTransaction(Request $request,GatewayTransaction $transaction)
    {

        try{
            
            $response = $this->checkPaymentStatus($transaction->reference);

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response);
    }

    
    /**
     * Creates a new Transaction for the link of that transaction
     *
     * @param  mixed $request
     * @return void
     */
    public function newTransaction(
        Request $request, 
        GatewayTransaction $transaction,
        NextPayClientAdapter $adapter)
    {
        try {

            $link = $transaction->link;

            if ($link) {
                $response = $adapter->handle($link);

            }else {

                throw new \InvalidArgumentException('Link model not found');
            }

        }catch(\Exception $e) {
            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response);
    }



    public function finalizeTransaction(
        Request $request, GatewayTransaction $transaction
    )
    {

        try{
           
            $data = $this->validate(
                $request,
                [
                'transfer_code'=>'required',
                'otp'=>'required'
                ]
            );

            $data['reference'] = $transaction->reference;
           
            
            $response = $this->finalizeOtpTransfers($data);
          

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);

        }

        return response()->json($response);
    }



    public function resendOtp(Request $request, GatewayTransaction $transaction)
    {

        try{

           $data = [
               'transfer_code'=> $transaction->transaction_id
           ];

           $response = $this->resendTransferOtp($data);

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);

        }

        return response()->json($response);
    }


    
    public function getTransaction(GatewayTransaction $transaction)
    {

        return response()->json($transaction);
    }


    
}
