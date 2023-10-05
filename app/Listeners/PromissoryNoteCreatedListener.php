<?php

namespace App\Listeners;

use App\Models\Staff;
use App\Models\Investor;
use App\Models\Affiliate;
use App\Helpers\FinanceHandler;
use App\Traits\SettleAffiliates;
use App\Helpers\TransactionLogger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PromissoryNoteCreatedListener
{

    use SettleAffiliates;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $data  = $event->payload;
      
        $receiverType = $data['receiverType'];
        $amount = $data['amount'];
        $tenure = $data['tenure'];
        $assignedPersonId = $data['assignedPersonId'];
        $note = $data['note'];

        if ($receiverType == 'App\Models\Admin') {
            return false;
        }
        $model = $this->determineReceivingModel($receiverType);

        $affiliate = $model->find($assignedPersonId);

        $financeHandler = new FinanceHandler(new TransactionLogger);

        $this->settleAffiliateOnPromissoryNote($affiliate, $note, $amount, $tenure, $financeHandler);

    }


    protected function determineReceivingModel($receiver)
    {
       
        switch($receiver) {
            case 'affiliate':
                $model = new Affiliate;
            break;

            case 'staff':
                $model = new Staff;
            break;

            case 'investor':
                $model = new Investor;
            break;

            default:
                throw new \InvalidArgumentException('No model found');
        }

        return $model;
    }
}
