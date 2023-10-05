<?php

namespace App\Models;

use Storage;
use Carbon\Carbon;
use App\Models\PaymentBuffer;
use App\Models\LoanTransaction;
use Illuminate\Support\Optional;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepaymentPlan extends Model
{

    use SoftDeletes;
    
    protected $guarded = [];
    
    protected $dates = ['payday'];

    protected $bufferCount = 3;
    
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function logs()
    {
        return $this->morphMany(Log::class, "resource");
    }

    public function okraSetup()
    {
        return $this->hasMany(OkraSetup::class);
    }
    
    public function buffers()
    {
      
        return $this->hasMany(PaymentBuffer::class, 'plan_id');
    }

    public function scopePaid($query)
    {
        return $query->where('status',1);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status',0);
    }
    
    public function repayments()
    {
        return $this->hasMany(Repayment::class, 'plan_id');
    }

    public function transactions()
    {
        return $this->hasMany(LoanTransaction::class,'plan_id');
    }
    
    public function getInvoiceAttribute($invoice)
    {
        return asset(Storage::url($invoice));        
    }

    public function isDue() 
    {
        $today = Carbon::today();

        return $this->status == false && $this->payday < $today;

    }
    
    public function scopeDue($query)
    {
        $today = Carbon::today();
        
        return $query->whereStatus(false)
                // ->whereMonth('payday', $today)
                // ->whereYear('payday', $today)
                // ->whereDate('payday', '<=', $today)
                ->where('order_issued', false);
    }

    public function scopeLoanActive($query)
    {
             
    }
    
    /**
     * Determines if a plan is eligible for a card attempt
     * For now this means now >= payday && now - payday < 2
     * 
     * @return bool
     */
    public function canMakeCardAttempt()
    {
        return 
            !$this->order_issued &&
            !$this->status &&
            now()->gte($this->payday) && 
            now()->diffInDays($this->pay) < 2 &&
            $this->loan->user->billingCards()->count() > 0;
    }


    public function clearBuffers()
    {
        return $this->buffers->each(function($item,$index){
            $item->delete();
        });
    }
    
    
    /**
     * Returns data for creating a Repayments's Invoice
     * 
     * @return array
     */
    public function getInvoiceData()
    {
        $user = $this->loan->user;
        
        $invoiceNumber = 'NPI' . $user->id . time();
        
        return [
            'user' => $user,
            'invoiceNumber' => $invoiceNumber,
            'dueDate' => $this->payday->toDateString(),
            'totalAmount' => $this->total_amount,
            'paymentItems' => [$this]
        ];
    }

    public function getEndBalanceAttribute()
    {
        return $this->balance;
    }


    // This is for old loans dont touch
    public function getBalanceDownAttribute()
    {
      return ($this->status == 0) ? $this->total_amount: 0;
    }

     // This is for old loans dont touch
    public function getAmountPaidAttribute()
    {
        return $this->status == 1 ? $this->total_amount : 0; 
    }

     // This is for old loans dont touch
    public function getTotalAmountAttribute()
    {
        // for new repayments management fees are already added to emi
        return ($this->is_new) ? $this->emi : $this->emi + $this->management_fee;
    }

    public function getAmountOwedAttribute()
    {
        return $this->status == 1 ? 0 : $this->total_amount;
    }


    //calculating the balances for old loans
    public function getPrevBalanceAttribute()
    {    
        $prev_balance = $this->loan->repaymentPlans->where('month_no','<',$this->month_no)->sum('balance_down');       
        return ($prev_balance);
    }


    public function getBalanceColumnDebit()
    {
        // The logic here is that if the last repay is negative meaning the customer is owing us
        // If the last repay is positive we owe the customer deficit

        // On the loan statement we subtract last repay from previous balance when last repay is positive
        $prev_balance = $this->loan->repaymentPlans->where('month_no','<=',$this->month_no)->where('status',0)->sum('emi');  
        $last_repay_wallet = optional( $this->loan->repaymentPlans->where('status',1)->last())->wallet_balance;
        if($last_repay_wallet < 0){
           
            return ($prev_balance + abs($last_repay_wallet));
        }else{
            return ($prev_balance - abs($last_repay_wallet));
        }     
      
      
    }

    public function getBalanceColumnDebitOldLoans()
    {
        $repayments = $this->loan->repaymentPlans;
        $prev_balance = $repayments->where('month_no','<=',$this->month_no)->where('status',0)->sum('emi') + $repayments->where('month_no','<=',$this->month_no)->where('status',0)->sum('management_fee');  
        $last_repay_wallet = optional($repayments->where('status',1)->last())->wallet_balance;
        if($last_repay_wallet < 0){
           
            return $prev_balance + abs($last_repay_wallet);
        }else{
            return $prev_balance - abs($last_repay_wallet);
        }     
      
      
    }

    public function getBalanceColumnCredit()
    {
        $credit = $this->wallet_changed ? $this->getFormerBalance() : $this->wallet_balance;
        $credit = $credit > 0 ? -$credit : abs($credit);
        return $credit;
    }

    /**
     * getFormerBalance
     * 
     *  This computes the previous value of a column before the use
     *  of transaction has altered the wallet balance for wallet_changed = true columns
     * @return void
     */
    public function getFormerBalance()
    {
        list($debit,$credit) = $this->transactions->partition(function($a){
            return $a->type == 1;
        });
        
        $debit = $debit->sum('amount');
        $credit = $credit->sum('amount');
        return ($this->wallet_balance + $debit ) - $credit;
        
    }
    
    /**
     * Checks if the loan of a plan is active
     *
     * @return boolean
     */
    public function loanActive()
    {
        return optional($this->loan)->status  == 1 && $this->loan->is_managed == false; 
    }

    
    /**
     * Creates a buffers for a repayment plan
     *
     * @return void
     */
    private function createBuffers()
    {
       
        DB::beginTransaction();
        
        try {
           // TODO ADD PAYSTACK CHARGE HERE
            $splitAmount = $this->totalAmount/3;
            $gatewayCharge = paystack_charge($splitAmount);
            $amount = $splitAmount + $gatewayCharge;

            $data = [
                'amount'=> round($amount, 2),
                'plan_id'=>$this->id,
                'requestId'=>time(),
                'mandateId'=> $this->loan->mandateId ?? 'No Mandate',
            ];
    
            for ($i = 0;$i < $this->bufferCount;$i++)
            {
                PaymentBuffer::create($data);
            }
           
        }catch(\Exception $e){
            DB::rollback();
            return false;
        }
        DB::commit();
      
      
        return $this->buffers()->get();
    }

    
    /**
     * Get all buffers attached  to a plan
     *
     * @return void
     */
    public function getBuffers()
    {
        $buffers = $this->hasBuffers ? $this->buffers()->get() : $this->createBuffers();
        return $buffers;
    }

    
    /**
     * Get total splits paid already
     *
     * @return double
     */
    public function totalSplitPayments() 
    {
        return $this->buffers->where('status', 1)->sum('amount');

    }
    
    /**
     * If a plan has buffers
     *
     * @return boolean
     */
    public function getHasBuffersAttribute()
    {
        return $this->buffers()->get()->count() > 0;
    }

    
    /**
     * Checks if any buffer is left unpaid
     *
     * @return void
     */
    public function allBuffersPaid()
    {
        return $this->buffers()->get()->where('status', 0)->first() == null;
    }


    
    /**
     * Returns repaymentplan before this in the loan schedule
     *
     * @return void
     */
    public function prevSibling()
    {
        if($this->month_no > 1) {

            return $this->loan->repaymentPlans->where('month_no',$this->month_no - 1)->first();
        }

        return null;
    }
    
    /**
     * Returns the repayment plan after this one in the loan schedule
     *
     * @return void
     */
    public function nextSibling()
    {
        if($this->month_no < $this->loan->duration){
            return $this->loan->repaymentPlans->where('month_no',$this->month_no + 1)->first();
        }
        return null;
    }

    public function okraLogs()
    {
        return $this->hasMany(OkraLogs::class);
    }

    
}
