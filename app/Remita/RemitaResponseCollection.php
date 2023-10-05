<?php
namespace App\Remita;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Unicredit\Utils\Response;

class RemitaResponseCollection extends Collection implements Response

{

    // public function __construct($items = [])
    // {
    //     parent::__construct();
    // }



    /**
     * isASuccess
     *  if Atleast one of the bits succeeded
     * @return bool
     */
    public function isASuccess()
    {
       
        return $this->where('isSuccessful',true)->first() !== null ? true : false;
    }


    /**
     * isActive
     *  If atleast one of them is active
     * @return string
     */
    public function isActive()
    {
        return $this->where('isActive',true)->first() ? true : false;
    }


    /**
     * getStatusCode
     *  Returns a string of all their status codes
     * @return string
     */
    public function getStatusCode()
    {
        return json_encode( $this->pluck('code'));
    }


    /**
     * getRRRs
     *  Get all the RRRs and return as string
     * @return string
     */
    public function getRRR()
    {
       
        return json_encode(@$this->pluck('rrr')[0]);
    }
    

    /**
     * getTransactionRefs
     *  Get all the transaction refs and return them as string
     * @return string
     */
    public function getTransactionRef()
    {
        return json_encode($this->pluck('transactionRef'));
    }
    

    /**
     * getRequestIds
     *  Get all the request ids and return them as string
     * @return string
     */
    public function getRequestId()
    {
        return json_encode($this->pluck('requestId'));
    }
    

    /**
     * getMessage
     *  Get all the messages and return them as string
     * @return string
     */
    public function getMessage()
    {
        return json_encode($this->pluck('message'));
    }


    public function getType()
    {
       return $this->first()->type;
    }


    /**
     * getRawData
     *  Returns all the raw data from al
     * @return string
     */
    public function getRawData()
    {
        return json_encode($this->pluck('rawData'));
    }


    

   
}

?>