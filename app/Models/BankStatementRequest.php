<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BankStatementRequest extends Model
{
    //

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    /**
     * Retrieveable
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeRetrievable($query)
    {
        return $query->where('request_doc', null)->whereNotNull('ticket_no')->whereStatus(1);
    }

    
}
