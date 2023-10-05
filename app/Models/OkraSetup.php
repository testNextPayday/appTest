<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OkraSetup extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'user_id','bank_response','bankId', 'customerId', 'credit_account', 'debit_account','payment_id', 'payment_ref', 'setup_fee'
    ];

    public function user()
    {
        return $this->bleongsTo(User::class);
    }
}
