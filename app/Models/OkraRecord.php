<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OkraRecord extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'user_id','bankId','customerId','recordId','account_id','balance_id'
    ];

    public function user()
    {
        return $this->bleongsTo(User::class);
    }
}
