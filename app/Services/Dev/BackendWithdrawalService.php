<?php
namespace App\Services\Dev;

use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use Illuminate\Database\Eloquent\Model;



class BackendWithdrawalService
{
    
    protected $financeHandler;

    protected $code;
    
    /**
     * withdrawer
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $withdrawer;

    protected $amount;

    public function __construct() {

        $this->financeHandler = new FinanceHandler(new TransactionLogger);
    }


    public function init(Model $withdrawer, $amount)
    {
        $this->code = config('unicredit.flow')['withdrawal'];

        $this->withdrawer = $withdrawer;

        $this->amount = $amount;

        $this->withdrawal = $this->withdrawer->withdrawals()->create(['amount'=> $this->amount]);

        $this->financeHandler->handleDouble(

            $this->withdrawer, $this->withdrawer, $this->amount , $this->withdrawal, 'WTE', $this->code
        );
    }


    public function end()
    {
        $this->withdrawal->update(['status'=> '2']);

        $this->code = config('unicredit.flow')['withdrawal_approval'];

        $this->financeHandler->handleSingle(
            $this->withdrawer, 'debit', $this->withdrawal->amount, $this->withdrawal, 'E', $this->code
        );


    }
}