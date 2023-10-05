<?php
namespace App\Card;
use JsonSerializable;

class ChargeResponse implements JsonSerializable

{

    public $status;
    protected $message;
    protected $dump;
    protected $type;
    protected $amount;
    protected $date;
    protected $reference;

    public function __construct(array $response)
    {
        $this->rawData = $response;
        $this->status = $response['data']['status'];
        $this->message = @$response['data']['gateway_response'];
        $this->dump = @$response['dump'];
        $this->type  = @$response['type'];
        $this->reference = @$response['data']['reference'];
        $this->date = @$response['data']['transaction_date'];
        $this->amount = @$response['data']['amount'];
       
    }


    public function getDate()
    {
        return \Carbon\Carbon::parse($this->date)->toDateTimeString();
    }

    public function getMessage()
    {
        return $this->message;
    }


    public function getRawData()
    {
        return $this->rawData;
    }

    public function getReference()
    {
        return $this->reference;
    }


    public function isSuccessful()
    {
        return $this->status;
    }

   
   

    public function JsonSerialize()
    {
        return get_object_vars($this);
    }


}

?>