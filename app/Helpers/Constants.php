<?php


namespace App\Helpers;

class Constants
{
    
    const DDM_REMITA = "100";
    
    const DDM_OKRA = "101";

    const DDM_MONO = "102";

    const DDM_LYDIA = "103";
    
    const DAS_REMITA = "200";
    
    const DAS_IPPIS = "201";
    
    const DAS_RVSG = "202";    

    const DAS_WISETRADER = "203";

    const DEFAULT_DEFAULT = "400";

    const WISETRADER_WISETRADER = "500";
    
    const CARDS_PAYSTACK = "300";
    
    const ADMIN_CODE = "001";
    
    const AFFILIATE_CODE = "004";
    
    
    /**
     * Returns a map of collections with code;
     */
    public static function generateCollectionCodeMap()
    {
        $collectionMethods = config('settings.collection_methods');
        
        $map = [];
        
        foreach($collectionMethods as $method => $values) {
            // ddm, das, card
            
            foreach($values as $code => $name) {
                $map[$code] = strtoupper("$method $name");        
            }
        }
        
        return $map;
    }
}