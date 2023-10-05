<?php
namespace App\Services;

use App\Models\User;
use App\Paystack\PaystackService;
use App\Models\GatewayTransaction;


class RefundTransactionService extends PaystackService
{

    
    /**
     * Makes a refund
     *
     * @param  mixed $reference
     * @return void
     */
    public function refund(string $reference, User $user)
    {
        $data = ['transaction'=> $reference];

        $this->setHttpResponse('/refund', 'GET', $data);

        $response = $this->retrieveResponse();

        $additionalLogData = [
            'recipient'=> $user
        ];
        
        $this->logGatewayTransaction($additionalLogData, $response);
    }

        
    /**
     * Logs gateway transaction
     *
     * @param  mixed $data
     * @return void
     */
    protected function logGatewayTransaction($data, $response)
    {
        $gatewayT = GatewayTransaction::create(
            [

            'owner_type'=>get_class($data['recipient']),

            'owner_id'=>$data['recipient']->id,

            'amount'=>@$response['data']['amount'],

            'reference'=>@$response['data']['reference'],

            'transaction_id'=>@$response['data']['id'],

            'description'=> 'Making Payment Refund',

            'pay_status'=> @$response['data']['status'] == "reversed" ? 1 : 0,

            'status_message'=>@$response['data']['status'] ,
            ]
        );

        
    }
}