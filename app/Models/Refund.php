<?php

namespace App\Models;

use App\Models\Loan;
use App\Models\User;
use App\Models\Staff;
use App\Traits\HasGatewayRecords;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasGatewayRecords;
    
    protected $guarded = [];

    function getUSerInfo(){
    	return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loan()
    {

        return $this->belongsTo(Loan::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    function loanInfo(){
    	return $this->hasOne('App\Models\Loan', 'id', 'loan_id');
    }
}
