<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use App\Helpers\FinanceHandler;
use App\Structures\FundTransferRequest;
use App\Helpers\TransactionLogger;

class FundTransferHandler

{


    protected $fundDirection;
    protected $sender;
    protected $amount;
    protected $recipient;

    public function __construct()
    {
        $this->financeHandler = new FinanceHandler(new TransactionLogger);
    
    }

    public function attempt(FundTransferRequest $request)
    {
        if($this->isPermitted($request)){

            $code = config('unicredit.flow')['wallet_vault_transfer'];
            $this->financeHandler->handleDouble(
                $request->sender,
                $request->recipient,
                $request->amount,
                $request->sender,
                $request->flow,
                $code
            );

            return true;
        }

        return false;
       

    }

    private function isPermitted($request)
    {
        $column = $this->getColumns($request->flow)[0];
        if($request->sender->{$column} < $request->amount) throw new \Exception('Invalid transfer amount');
        return true;
    }

    private function getColumns($flow)
    {
        return explode('-', config("unicredit.asset_flow")[$flow]);
    }

   
}

?>