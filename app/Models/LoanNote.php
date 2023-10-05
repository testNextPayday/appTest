<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanNote extends Model
{
    //

    protected $guarded = [];

    
    /**
     * the onwer of the loan notes
     *
     * @return void
     */
    public function owner()
    {
        return $this->morphTo('owner');
    }
}
