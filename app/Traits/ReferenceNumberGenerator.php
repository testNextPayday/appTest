<?php

namespace App\Traits;

use Keygen\Keygen;

trait ReferenceNumberGenerator {
    
    private function generateKey($prefix = null, $length = 9)
    {
        // prefixes the key with a random integer between 1 - 9 (inclusive)
        return Keygen::numeric($length)->prefix(mt_rand(1, 9))
                ->prefix($prefix ?? $this->refPrefix)
                ->generate(true);
    }

    public function generateReference($model = null, $prefix = null)
    {
        $reference = $this->generateKey($prefix);
        
        
        $query = $model ? $model::whereReference($reference) : 
                self::whereReference($reference);
        
        // Ensure ID does not exist
        // Generate new one if ID already exists
        while ($query->count() > 0) {
            $reference = $this->generateKey();
        }

        return $reference;
    }    


    public function generateToken($model)
    {
        $token = $this->generateKey();
        $query = $model ? $model::whereToken($token) : self::whereToken($token);
        while ($query->count() > 0) {
            $token = $this->generateKey();
        }

        return $token;
    }
    
    public static function generateCode($prefix = "UC-", $length = 5)
    {
        return Keygen::numeric($length)->prefix(mt_rand(1, 9))->prefix($prefix)->generate(true);
    }

    public function generateRemitaCode($model = null)
    {
        $code = $this->generateKey("");
        
        
        $query = $model ? $model::whereReference($code) : 
                self::whereRemitaAuthCode($code);
        
        // Ensure ID does not exist
        // Generate new one if ID already exists
        while ($query->count() > 0) {
            $code = $this->generateKey("");
        }

        return $code;
    }
}