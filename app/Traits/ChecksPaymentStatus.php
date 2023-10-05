<?php
namespace App\Traits;

use App\Models\GatewayTransaction;

/** This trait is used by the moneyGram class or any other class that has a channel and a dblogger */

trait ChecksPaymentStatus
{
    
    /**
     * checkStatus
     *
     * @param  mixed $payload a transaction reference as an array or a string
     * @return array | \Exception
     */
    public function checkPaymentStatus($payload)
    {
        if (is_array($payload)) {

           $response =  $this->checkPaymentArrayStatus($payload);

           return $response;
        }

        if (is_string($payload)) {

           $response =  $this->gatewayCheck($payload);

           return $response;
        }

        throw new \InvalidArgumentException("Unallowed type passed for payment status check");
    }


    private function checkPaymentArrayStatus($payload)
    {
        $statusList = [];

        foreach($payload as $index=>$value){

            $statusList[] = $this->gatewayCheck($value);
        }

        return $statusList;
    }

    private function gatewayCheck($value)
    {
       

        $gatewayT = GatewayTransaction::whereReference($value)->first();

        if ($gatewayT) {
            
            $data = [
                'reference'=>$gatewayT->reference,
                'amount'=>$gatewayT->amount,
                'recipient_code_or_id'=>$gatewayT->owner->recipient_code
            ];
            $response = $this->channel->verifyTransfer($data);

            $response_status = $response['data']['status'];

            $response_implies_status = $response_status == 'success' ? 1 : 0;

            $gatewayT->update(
                [
                'status_message'=>$response_status, 
                'pay_status'=>$response_implies_status
                ]
            );


            // if its a withrawal request lets update it 
            if ($response_status == 'success' && $gatewayT->link_type == 'App\Models\WithdrawalRequest') {

                $request = $gatewayT->link;

                $request->update(['status'=>2]);
            }

            return 'Transaction Status '.$gatewayT->status_message;
            
        }

        throw new \InvalidArgumentException("Invalid transaction reference given to check payment status");

        
    }



    public function finalizeOtpTransfers($data)
    {

       $responseCode = $this->channel->finalizeTransfer($data);

       if($responseCode == 400){

            throw new \BadMethodCallException(" An error occurred. Transaction was not successful");
       }

       return $this->checkPaymentStatus($data['reference']);

    }


    public function resendTransferOtp($data)
    {
        $response = $this->channel->resendTransferOtp($data);

        return $response;
    }
}
?>