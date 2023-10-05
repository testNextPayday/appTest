<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromissoryNoteSetting extends Model
{
    //

    protected $guarded = [];

    public function scopeUpfrontTaxRate($query)
    {
        $upfrontTaxRate = $query->where('name', 'like','%Upfront%')->first();
        return $upfrontTaxRate ? (int) $upfrontTaxRate->tax_rate : 0;
    }

    public function scopeMonthlyTaxRate($query)
    {
        $monthlyTaxRate = $query->where('name', 'Monthly Interest Plan')->first();
        return $monthlyTaxRate ? (int) $monthlyTaxRate->tax_rate : 0;
    }

    public function scopeBackendTaxRate($query)
    {
        $backendTaxRate = $query->where('name', 'Backend Interest plan')->first();
        return $backendTaxRate ? (int) $backendTaxRate->tax_rate : 0;
    }

    public function scopeUpfrontInterestRate($query)
    {
        $upfrontInterestRate = $query->where('name', 'Upfront Interest plan')->first();
        return $upfrontInterestRate ? (int) $upfrontInterestRate->interest_rate : 0;
    }

    public function scopeMonthlyInterestRate($query)
    {
        $monthlyInterestRate = $query->where('name', 'Monthly Interest Plan')->first();
        return $monthlyInterestRate ? (int) $monthlyInterestRate->interest_rate : 0;
    }

    public function scopeBackendInterestRate($query)
    {
        $backendInterestRate = $query->where('name', 'Backend Interest plan')->first();
        return $backendInterestRate ? (int) $backendInterestRate->interest_rate : 0;
    }
}
