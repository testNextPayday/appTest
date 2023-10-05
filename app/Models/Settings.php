<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['slug', 'name', 'value'];

    public function scopeManagementFee($query)
    {
        $fee = $query->where('slug', 'monthly_management_fee')->first();
        return $fee ? (float) $fee->value : 1.5;
    }


    public function scopeSalaryNowEmployerID($query)
    {
        $emp = $query->where('slug', 'salary_now_employer')->first();
        return $emp ? (int)$emp->value : 260;
    }

    public function scopeSettlingMaturedLoanPercent($query)
    {
        $percent = $query->where('slug', 'settling_matured_loans_percent')->first();
        return $percent ? (double)$percent->value : 1.5;
    }


    public function scopeBankStatmentPeriodMonths($query)
    {
        $period = $query->where('slug', 'bank_statement_period_months')->first();
        return $period ? (float) $period->value : 3;
    }

    public function scopeBankStatmentEnabled($query)
    {
        $settings = $query->where('slug', 'bank_statement_enabled')->first();
        return $settings ? (float) $settings->value : 1;
    }

    public function scopeInvestorPromissoryNoteCommissionRate($query)
    {
        $rate = $query->where('slug', 'promissiory_note_affiliate_rate')->first();
        return $rate ? (float) $rate->value : 0;
    }

    public function scopeInvestorVerificationFee($query)
    {
        $fee = $query->where('slug', 'lender_activation_fee')->first();
        return $fee ? (float) $fee->value : 0;
    }

    public function scopeLoanRequestFee($query)
    {
        $fee = $query->whereSlug('loan_request_processing_fee')->first();
        return $fee ? (float) $fee->value : 0;
    }

    public function scopeAffiliateCommissionRate($query)
    {
        $rate = $query->whereSlug('affiliate_commission_rate')->first();
        return $rate ? (float) $rate->value : 0;
    }

    public function scopeBorrowerCommissionRate($query)
    {
        $rate = $query->whereSlug('borrower_commission_rate')->first();
        return $rate ? (float) $rate->value : 0;
    }

    public function scopeInvestorFundingCommissionRate($query)
    {
        $rate = $query->whereSlug('investor_funding_commission_rate')->first();
        return $rate ? (float) $rate->value : 0;
    }

    public function scopeSupervisorCommissionRate($query)
    {
        $rate = $query->whereSlug('supervisor_commission_rate')->first();
        return $rate ? (float) $rate->value : 0;
    }

    public function scopeAffiliateMinimumWithdrawal($query)
    {
        $rate = $query->whereSlug('affiliate_minimum_withdrawal')->first();
        return $rate ? (float) $rate->value : 0;
    }

    public function scopeAffiliateVerificationFee($query)
    {
        $fee = $query->where('slug', 'affiliate_verification_fee')->first();
        return $fee ? (float) $fee->value : 0;
    }

    public function scopePortfolioManagementPercentage($query)
    {
        $fee = $query->where('slug', 'portfolio_management_percentage_fee')->first();
        return $fee ? (float) $fee->value : 0;
    }


    public function scopeInvestorMinimumWalletBalance($query)
    {
        $balance = $query->where('slug', 'investor_minimum_wallet')->first();
        return $balance ? (float) $balance->value : 0;
    }


    public function scopeAffiliateRepaymentCommission($query)
    {
        $percentage = $query->where(
            'slug',
            'affiliate_repayment_commission'
        )->first();
        return $percentage ? (float) $percentage->value : 0;
    }


    /**
     * Checks to see if loan processing is disabled
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeLoanProcessingDisabled($query)
    {
        $value = $query->where('slug', 'enable_loan_request')->first()->value;
        return $value == 0;
    }
}
