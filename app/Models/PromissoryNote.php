<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\PromissoryPaymentMethod;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ReferenceNumberGenerator;

class PromissoryNote extends Model
{
    //
    use PromissoryPaymentMethod, ReferenceNumberGenerator;


    protected $guarded = [];

    protected $refPrefix = 'NPRN-';
    
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            $model->reference = $model->generateReference();
        });
    }


    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }



    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    
    /**
     * Anniversary of the note
     *
     * @return void
     */
    public function isDayAnniversary()
    {
        $today = Carbon::today();

        $startDate = Carbon::parse($this->start_date);

        $todayString = $today->toDateString();

        $startDateString = $startDate->toDateString();

        // checks the days are same and the dates are not exactly the same
        if ($today->day == $startDate->day && strcmp($startDateString, $todayString) < 0) {

            return true;
        }

        return false;
    }
    
    public function getRouteKeyName()
    {
        return 'reference';
    }

    public function certificate()
    {
        return $this->hasOne(\App\Models\InvestmentCertificate::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\PromissoryNoteTransaction::class);
    }

    public function getTaxPaidAttribute()
    {
        return $this->transactions->where('code', '025')->sum('amount');
    }

    public function investor()
    {
        return $this->belongsTo(\App\Models\Investor::class);
    }


    public function isBackend()
    {
        return strtolower($this->interest_payment_cycle) == 'backend';
    }

    public function isUpfront()
    {
        return strtolower($this->interest_payment_cycle) == 'upfront';
    }

    public function isMonthly()
    {
        return strtolower($this->interest_payment_cycle) == 'monthly';
    }

    // public function getTaxAttribute()
    // {
    //     return $this->certificate->tax;
    // }


    public function getCertificateUrlAttribute()
    {
        return optional($this->certificate)->certificate ?? '#';
    }


    public function requiresLetter()
    {
        return $this->amount >= 1000000;
    }

    
}
