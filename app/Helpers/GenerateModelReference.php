<?php
namespace App\Helpers;

use App\Traits\ReferenceNumberGenerator;
/**
 *  This class is created to generate a reference number for any class 
 * that needs a reference number to be generated for it
 */
class GenerateModelReference
{

    use ReferenceNumberGenerator;

    protected $refPrefix = 'UC-WT-';


    public function generateModelReference($model)
    {
        return $this->generateReference($model);
    }
}