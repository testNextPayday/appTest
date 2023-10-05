<?php

namespace App\Models;

use App\Models\Bill;
use Illuminate\Database\Eloquent\Model;

class BillCategory extends Model
{
    
    protected $fillable = ['name'];

    /**
     * Bills belonging to this category
     *
     * @return \Illuminate\Support\Facade\Collection
     */
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
