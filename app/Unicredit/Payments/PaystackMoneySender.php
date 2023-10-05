<?php
namespace App\Unicredit\Payments;

use GuzzleHttp\Client;
use InvalidArgumentException;
use App\Traits\staticCallable;
use App\Paystack\PaystackService;
use Illuminate\Support\Facades\Config;
use App\Unicredit\Contracts\MoneySender;


class PaystackMoneySender extends PaystackService implements MoneySender
{
    

    /** This is the source params required by paystack initiate transaction */
    public $source = 'balance';

    /** The recipient type on paystack required for using account numbers */
    public $type = 'nuban';

    public $resendOtpReason = 'transfer';


    public function createRecipient($data)
    {

        validateFields($data, "type", "name", "account_number", "bank_code");

        return $this->setHttpResponse("/transferrecipient","POST",$data)->retrieveResponse();
    }


    public function getAllRecipients()
    {
        
        return $this->setHttpResponse(
            "/transferrecipient", "GET", $data  = []
        )->retrieveResponseData();
    }


    // Methods for transfers starts here
    public function createTransfer($data)
    {
        validateFields($data, "source", "amount", "recipient");
        $data['reference'] = generateHash();
        return $this->setHttpResponse(
            "/transfer", "POST", $data
        )->retrieveResponse();
    }


    protected function getAllTransfers()
    {
        
        return $this->setHttpResponse("/transfer", "GET", $data = []);
    }

    protected function fetchTransfer($code)
    {
        if(is_null($code)) 
            throw new InvalidArgumentException(" A transfer code is needed to fetch transfer");
       
        return $this->setHttpResponse("/transfer/$code", "GET", $data = [])->retrieveResponse();
    }

    public function finalizeTransfer($data)
    {
        validateFields($data, "transfer_code", "otp");
       
        // am returning response because the data it returns is empty so i can check the code it returns 
        return $this->setHttpResponse("/transfer/finalize_transfer", "POST", $data)->getStatusCode();
    }

    public function verifyTransfer($data)
    {
        $reference  = $data['reference'];
        if(is_null($reference)) 
            throw new InvalidArgumentException(" A transaction reference is needed to verify Transaction");

       
        return $this->setHttpResponse("/transfer/verify/$reference","GET",$data)->retrieveResponse();
    }



    public function bulkTransfer($data)
    {
        $data['source'] = $this->source;
        return $this->setHttpResponse("/transfer/bulk", "POST", $data)->retrieveResponse();
    }
    // Methods for transfers ends here


   


    // The methods below are for controlling transfers

    public function checkBalance()
    {
        
        return $this->setHttpResponse("/balance","GET",$data = [])->retrieveResponseData();
    }

    public function resendTransferOtp($data)
    {
        validateFields($data,"transfer_code");

        $data['reason'] = $this->resendOtpReason;
        
        return $this->setHttpResponse("/transfer/resend_otp","POST",$data)->retrieveResponse();
    }

    public function disableOtp()
    {
        
        return $this->setHttpResponse("/transfer/disable_otp","POST",$data = [])->retrieveResponse();
    }

    public function finalizeDisableOtp($data)
    {
       
        validateFields($data,"otp");
        
        return $this->setHttpResponse("/transfer/disable_otp_finalize","POST",$data)->retrieveResponse();
    }

    public function enableOtp()
    {
        
        return $this->setHttpResponse("/transfer/enable_otp","POST",$data = [])->retrieveResponse();
    }

    public function ledgerHistory($data)
    {
        return $this->setHttpResponse("/balance/ledger","GET",$data)->retrieveResponse();
    }
    
}

?>