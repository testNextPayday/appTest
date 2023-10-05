<?php
namespace App\Unicredit\Managers;

use App\Models\BankDetail;
use App\Models\GatewayTransaction;
use App\Traits\ChecksPaymentStatus;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Contracts\MoneySender;
use App\Unicredit\Exceptions\MoneyGramRecipientCreationException;


class MoneyGram
{

   
    use ChecksPaymentStatus;

    protected $recipientDetails;

    protected $amount;

    protected $response;

    
    public function __construct(MoneySender $channel, DatabaseLogger $dbLogger)
    {

        $this->channel = $channel;
        $this->dbLogger = $dbLogger;
    }


    public function makeTransfer(BankDetail $recipientDetails,array $data)
    {
        
        validateFields($data, "amount");

        $this->recipientDetails = $recipientDetails;
        $data['recipient'] = $recipientDetails;

        $this->amount = $data['amount'];

        $code  = $this->recipientDetails->recipient_code;
       
        if (is_null($code) || empty($code)) {
           
            // all we do here is attempt to create a recipient
            $this->response = $this->attemptCreateRecipient();
          
            if( (!@$this->response['status']) || (!@$this->response['data']['active'])){

                // log it and exit not our problem
                return $this->sendFailedRecipientCreationResponse();
            }

            // successful update details with code
            $this->recipientDetails->update([

                'recipient_code'=>@$this->response['data']['recipient_code']
            ]);

            $this->recipientDetails->refresh();
        }

        $transferData = [

            'source'=>$this->channel->source,
            'amount'=>intval($this->amount * 100),
            'recipient'=>$this->recipientDetails->recipient_code,
            'reason'=> @$data['reason'] ?? 'Payment from Nextpayday'
        ];


       
        // we are sure recipient exists on the gateway
        $this->response = $this->channel->createTransfer($transferData);

        // store the transfer response while we verify
        $this->transferResponse = $this->response;

        //verify transfer 
        $this->response = $this->channel->verifyTransfer(
            $verifyData = $this->getVerifyDetails()
        );

        $data['account_name'] =  @$this->response['data']['recipient']['name'];

        //create a gateway response  for this transaction
        $this->logGatewayTransaction($data);
        
        if ($this->response['status']) {

            return $this->sendOkTransferResponse();
        }

        return $this->sendBadRequestResponse();
        
    }


    public function makeBulkTransfer($data)
    {
        
        $transfers = $data['transfers'];

        foreach ($transfers as $key=>$recipient){
            //convert amount to kobo
            $transfers[$key]['amount'] = intval($transfers[$key]['amount']) * 100;
            validateFields($recipient, "amount", "recipient", "reference");
        }
        $data['transfers']  = $transfers;
       
        $this->transferResponse = $this->channel->bulkTransfer($data);
        
        if ($this->transferResponse['status']) {

            //loop through each and create gateway transaction
            foreach ($this->transferResponse['data'] as $index=>$recipient)
            {
                $bank = BankDetail::where('recipient_code', $recipient['recipient'])->first();
               
                $data['recipient'] = $bank;
                $data['reference'] = $data['transfers'][$index]['reference'];
                $data['transfer_code'] = $recipient['transfer_code'];
                $data['amount'] = $recipient['amount']/100;// convert amount back to naira
                $data['link'] = $bank->owner;
                $data['status_message'] = 'pending';

                $this->logGatewayTransaction($data);
            }

            return $this->sendOkTransferResponse();
        }

        return $this->sendBadRequestResponse();

    }


    protected function sendOkTransferResponse()
    {

        return [
            'message'=>$this->transferResponse['message'],
            'status'=>$this->response['data']['status'],
            'transfer_code'=>@$this->response['data']['transfer_code'],
            'amount'=>@$this->response['data']['amount']
        ];

    }

    protected function sendBadRequestResponse()
    {
        return [
            'message'=>$this->response['message'],
            'status'=>$this->response['data']['status'],
            'transfer_code'=>$this->response['data']['transfer_code'],
            'amount'=>$this->response['data']['amount']
        ];

    }

    protected function attemptCreateRecipient()
    {
        return $this->channel->createRecipient(
            [

            'type'=>$this->channel->type,
            'name'=>$this->recipientDetails->owner->name,
            'account_number'=>$this->recipientDetails->account_number,
            'bank_code'=>$this->recipientDetails->bank_code
            ]
        );

        
    }


    protected function logRecipientCreationResponse()
    {
        $this->dbLogger->log(
            $this->recipientDetails,
            [
            'title'=> 'Transfer Recipient Creation at Gateway',

            'description'=> ' An attempt to create Recipient Failed',

            'status'=> 0,

            'data_dump'=>json_encode($this->response)
            
            ]
        );
    }

    protected function sendFailedRecipientCreationResponse()
    {
        
        $this->logRecipientCreationResponse();

        throw new MoneyGramRecipientCreationException("Transfer recipient cannot be created at gateway");
    }

    protected function getVerifyDetails()

    {
        return [ 
            'reference'=> @$this->response['data']['reference'], 
            'amount'=>@$this->response['data']['amount']/100,
            'recipient_code_or_id'=>$this->recipientDetails->recipient_code,
        ];
    }


    protected function logGatewayTransaction($data)
    {
        $gatewayT = GatewayTransaction::create(
            [

            'owner_type'=>get_class($data['recipient']),

            'owner_id'=>$data['recipient']->id,

            'amount'=>$data['amount'],

            'reference'=>@$this->transferResponse['data']['reference'] ?? $data['reference'],

            'transaction_id'=>@$this->transferResponse['data']['transfer_code'] ?? $data['transfer_code'],

            'description'=> @$data['account_name'] ?? ( @$data['type'] ?? 'Money Transfer'),

            'pay_status'=> @$this->transferResponse['data']['status'] == "success" ? 1 : 0,

            'status_message'=>@$this->transferResponse['data']['status'] ?? @$data['status_message'],

            'link_id'=> @$data['link']->id,

            'link_type'=> get_class($data['link'])
            ]
        );

        
    }


    
}

?>