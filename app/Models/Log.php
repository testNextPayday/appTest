<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = [];
    
    public function resource()
    {
        return $this->morphTo();
    }
    
    public function resourceLink($prefix)
    {
        $resource = $this->resource;
        
        if (!$resource) return '#';
        
        $resourceClass = get_class($resource);
        
        switch($resourceClass) {
            case 'App\Models\RepaymentPlan':
                return route("$prefix.loans.view", ['reference' => $resource->loan->reference]);
            case 'App\Models\Loan':
                return route("$prefix.loans.view", ['reference' => $resource->reference]);
            case 'App\Models\LoanRequest':
                return route("$prefix.loan-requests.view", ['reference' => $resource->reference]);
            default:
                return '#';
        };
    }
}
