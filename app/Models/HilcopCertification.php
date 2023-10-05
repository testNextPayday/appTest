<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Keygen\Keygen;
use Illuminate\Support\Facades\Storage;

class HilcopCertification extends Model
{
    //
    protected $guarded = [];

    public function getCertificateAttribute($certificate)
    {
        return asset(Storage::url($certificate));    
    }
    
    private function generateKey()
    {
        // prefixes the key with a random integer between 1 - 9 (inclusive)
        return Keygen::numeric(9)->prefix(mt_rand(1, 9))->prefix('HCC-')->generate(true);
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
}
