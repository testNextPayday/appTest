<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Storage;
use Keygen\Keygen;

class InvestmentCertificate extends Model
{
    protected $guarded = [];
    
    // protected $dates = ['start_date'];
    
    public function getCertificateAttribute($certificate)
    {
        return asset(Storage::url($certificate));    
    }
    
    private function generateKey()
    {
        // prefixes the key with a random integer between 1 - 9 (inclusive)
        return Keygen::numeric(9)->prefix(mt_rand(1, 9))->prefix('UCE-')->generate(true);
    }

    public function generateReference()
    {
        $reference = $this->generateKey();
        
        // Ensure ID does not exist
        // Generate new one if ID already exists
        while (InvestmentCertificate::whereReference($reference)->count() > 0) {
            $reference = $this->generateKey();
        }

        return $reference;
    }

    public function scopeMatured($query)
    {
        return $query->where('maturity_date', '<', now())->where('status', 2)->orWhere('status', 2);
    }

    public function scopeActive($query)
    {
        return $query->where('maturity_date', '>=', now())->where('status', 1)->orWhere('status', 1);
    }
}
