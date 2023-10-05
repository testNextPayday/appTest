<?php
namespace App\Services\Investor;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PromissoryNote;
use App\Helpers\FinanceHandler;
use App\Models\WithdrawalRequest;
use App\Helpers\TransactionLogger;
use App\Models\PromissoryNoteTransaction;
use App\Services\Investor\PromissoryNoteService;
use App\Unicredit\Contracts\IPromissoryPaymentService;

/**
 *  Responsible for creating withdrawals for a promissory note when they are due
 */

abstract class PromissoryPaymentService implements IPromissoryPaymentService
{

    protected $promissoryNote;


    public function __construct(PromissoryNote $note)
    {
        $this->promissoryNote = $note;

    }
    
    /**
     * Writes a transaction to log
     *
     * @param  double $amount
     * @param  string $type
     * @param  integer $code
     * @return void
     */
    public function toPromissoryLog($amount, $type, $code)
    {
        $transaction = new PromissoryNoteTransaction;
        $transaction->amount = round($amount, 2);
        $transaction->direction = $type == 'debit' ? 2 : 1;
        $transaction->code = $code;
        $transaction->description = $this->getDescription($code);
        $transaction->promissory_note_id = $this->promissoryNote->id;
        $transaction->investor_id = $this->promissoryNote->investor->id;
        $transaction->save(); 

        return true;
    }

    
    /**
     * Builds Transaction Description
     *
     * @param  mixed $code
     * @return void
     */
    protected function getDescription($code)
    {
        $codeData = @@config('unicredit.flow_codes')[$code][0];
        $addendum = is_object($this->promissoryNote) && $this->promissoryNote->reference ? " for " . $this->promissoryNote->reference : '';
        return $codeData .    $addendum;

    }

    
    /**
     * Creates Withdrawal on PR Notes
     *
     * @param  double $amount
     * @return void
     */
    protected function createWithdrawals($amount)
    {
        $amount = floatval($amount);

        $this->creditWallet($amount);

        $financeHandler = new FinanceHandler(new TransactionLogger);

        $withdrawData = ['amount'=> $amount];

       

        $investor = $this->promissoryNote->investor;

        $withdrawal = $investor->withdrawals()->create($withdrawData);

        $code = config('unicredit.flow')['withdrawal'];

        $financeHandler->handleDouble(

            $investor, $investor, $withdrawData['amount'] , $withdrawal, 'WTE', $code
        );

        return true;
    }

    
    /**
     * Credits Investor Wallet before creating withdrawal
     *
     * @param  mixed $amount
     * @return void
     */
    protected function creditWallet($amount) 
    {
        $financeHandler = new FinanceHandler(new TransactionLogger);

        $investor = $this->promissoryNote->investor;

        $code = config('unicredit.flow')['promissory_note_interest'];

        $financeHandler->handleSingle($investor, 'credit', $amount, $this->promissoryNote, 'W', $code);
    }

    
    /**
     * Carries out monthly task
     *
     * @return void
     */
    public function monthlyTask()
    {
        $monthsLeft = $this->promissoryNote->monthsLeft;

        $tenure = $this->promissoryNote->tenure;

        if ($monthsLeft > 0 && $monthsLeft <= $tenure) {

            $this->payInterest();

            // Decrease the payments left
            $monthsLeft = $monthsLeft - 1;

            $this->promissoryNote->update(['monthsLeft'=> $monthsLeft]);

            return true;
        }

        return false;
        
       
    }

    
    /**
     * Checks if a note is ended
     *
     * @return void
     */
    public function checkEnded()
    {
        $monthsLeft = $this->promissoryNote->monthsLeft; 
        
        if ( $monthsLeft == 0 ) {

            $this->promissoryNote->update(['status'=> 2]);

            return true;
        }

        return false;
    }


    
    /**
     * Liquidates a Promissory Note
     *
     * @return void
     */
    public function liquidate()
    {
        $amount = $this->promissoryNote->payable_value;

        $this->createWithdrawals($amount);

        $this->promissoryNote->update(['status'=> 4]); // pushed  liquidated

        if ($certificate = $this->promissoryNote->certificate) {
            $certificate->update(['status'=> 2]);
        }
    }

    
    /**
     * RollOver a Promissory Note
     *
     * @return void
     */
    public function rollover()
    {
        $amount = $this->promissoryNote->payable_value;

        $this->spawnNewInvestment($amount);

        $this->promissoryNote->update(['status'=> 3]); // pushed rollover

        if ($certificate = $this->promissoryNote->certificate) {
            $certificate->update(['status'=> 2]);
        }
    }

    
    /**
     * Spawn a new note from new one
     *
     * @return void
     */
    protected function spawnNewInvestment($amount)
    {
        $today = Carbon::today();
        $note = $this->promissoryNote;
        $investor = $note->investor;

        if (! $note->tax ) {
            throw new \Exception(' Cannot rollover this note . Please withdraw');
        }
        $request = new Request([
            'start_date'=> $today->toDateString(),
            'amount'=> $amount,
            'tenure'=> $note->tenure,
            'interest_payment_cycle'=> $note->interest_payment_cycle,
            'rate'=> $note->rate,
            'tax'=> $note->tax,
            'previous_note_id'=> $note->id
        ]);

        $noteService = new PromissoryNoteService();

        $noteService->createPromissoryNote($request, $investor);

    }



    
    /**
     * Withdraw a Promissory Note
     *
     * @return void
     */
    public function withdraw()
    {
        $amount = $this->promissoryNote->payable_value;

        $this->createWithdrawals($amount);

        $this->promissoryNote->update(['status'=> 2]); // pushed withdrawn

        if ($certificate = $this->promissoryNote->certificate) {
            $certificate->update(['status'=> 2]);
        }
    }
}