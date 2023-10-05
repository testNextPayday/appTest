<?php
namespace App\Structures;

use App\Models\Investor;
use Illuminate\Http\Request;

class FundTransferRequest
{

    protected $request;
    public $sender;
    public $recipient;
    public $amount;
    public $flow;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->buildFundTransferObject();
    }

    /**
     * buildFundTransferObject
     *  returns an object that can be transferrable 
     * @return void
     */
    private function buildFundTransferObject()
    {
        $this->amount = $this->request->amount;
        $this->flow = $this->request->flow;
        $this->sender = $this->resolveSender();
        $this->recipient = $this->resolveRecipient();

        return $this;
    }

    /**
     * resolveSender
     *  Tells us who is sending the fund (who to debit)
     * @return \Illuminate\Database\Eloquent\Model || \Exception
     */
    private function resolveSender()
    {
        $sender = Investor::find($this->request->sender_id);
        if(! $sender) throw new \Exception('Invalid Sender specified');
        return $sender;
    }

    
    /**
     * resolveRecipient
     *  Tells us who is recieving the fund (who to credit )
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function resolveRecipient()
    {
       $id = $this->request->recipient_id ? $this->request->recipient_id : $this->request->sender_id;
       $recipient = Investor::find($id);
       if(! $recipient) throw new \Exception('Invalid Recipient specified');
       return $recipient;
    }


}
?>