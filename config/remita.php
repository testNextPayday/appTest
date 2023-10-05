<?php

return [

    'MERCHANTID' => getenv('MERCHANTID'),

    'SERVICETYPEID' => getenv('SERVICETYPEID'),

    'APIKEY' => getenv('APIKEY'),
 
    'CHECKSTATUSURL' => getenv('CHECKSTATUSURL'),
    
    'GATEWAYURL' => getenv('GATEWAYURL'),
    
    'PATH' => getenv('PATH'),
    
    'apiKey' => getenv('REMITA_API_KEY'),
    
    'apiToken' => getenv('REMITA_API_TOKEN'),
    
    'merchantId' => getenv('REMITA_MERCHANT_ID'),
    
    'serviceTypeId' => getenv('REMITA_SERVICE_TYPE_ID'),
    
    'baseUrl' => getenv('REMITA_BASE_URL'),
    
    'loanBaseUrl' => getenv('REMITA_LOAN_BASE_URL'),
    
    'das-mode' => getenv('REMITA_DAS_MODE'),
    
    'ddm-mode' => getenv('REMITA_DDM_MODE'),
    
    'das' => [
        'baseUrl' => getenv('REMITA_DAS_BASE_URL'),
        
        'apiKey' => getenv('REMITA_DAS_API_KEY'),
        
        'apiToken' => getenv('REMITA_DAS_API_TOKEN'),
        
        'merchantId' => getenv('REMITA_DAS_MERCHANT_ID'),
    ],
    
    'das-test' => [
        'baseUrl' => getenv('REMITA_DAS_BASE_URL_TEST'),
        
        'apiKey' => getenv('REMITA_DAS_API_KEY_TEST'),
        
        'apiToken' => getenv('REMITA_DAS_API_TOKEN_TEST'),
        
        'merchantId' => getenv('REMITA_DAS_MERCHANT_ID_TEST'),
    ],
    
    'ddm' => [
        
        'baseUrl' => getenv('REMITA_DDM_BASE_URL'),
        
        'apiKey' => getenv('REMITA_DDM_API_KEY'),
        
        'apiToken' => getenv('REMITA_DDM_API_TOKEN'),
        
        'merchantId' => getenv('REMITA_DDM_MERCHANT_ID'),
        
        'serviceTypeId' => getenv('REMITA_DDM_SERVICE_TYPE_ID'),
        
    ],
    
    'ddm-test' => [
        
        'baseUrl' => getenv('REMITA_DDM_BASE_URL_TEST'),
        
        'apiKey' => getenv('REMITA_DDM_API_KEY_TEST'),
        
        'apiToken' => getenv('REMITA_DDM_API_TOKEN_TEST'),
        
        'merchantId' => getenv('REMITA_DDM_MERCHANT_ID_TEST'),
        
        'serviceTypeId' => getenv('REMITA_DDM_SERVICE_TYPE_ID_TEST'),
    ],
    
    // 'banks' => [
    //     '023' => 'CITIBANK NIG LTD',
    //     '214' => 'FIRST CITY MONUMENT BANK PLC',
    //     '011' => 'FIRST BANK OF NIGERIA PLC',
    //     '035' => 'WEMA BANK PLC',
    //     '039' => 'STANBIC IBTC BANK PLC',
    //     '033' => 'UBA PLC',
    //     '101' => 'PROVIDUS BANK PLC',
    //     '044' => 'ACCESS BANK PLC',
    //     '050' => 'ECOBANK NIGERIA PLC',
    //     '057' => 'ZENITH BANK PLC',
    //     '063' => 'DIAMOND BANK PLC',
    //     '076' => 'POLARIS BANK PLC',
    //     '082' => 'KEYSTONE BANK',
    //     '232' => 'STERLING BANK PLC',
    //     '032' => 'UNION BANK OF NIGERIA PLC',
    //     '030' => 'HERITAGE BANK',
    //     '301' => 'JAIZ BANK PLC',
    //     '058' => 'GUARANTY TRUST BANK PLC',
    //     '070' => 'FIDELITY BANK PLC',
    //     '068' => 'STANDARD CHARTERED BANK NIGERIA LTD',
    //     '100' => 'SUNTRUST BANK NIG LTD',
    //     '215' => 'UNITY BANK PLC',
    //     '068' => 'STANDARD CHARTERED BANK NIGERIA LTD',
    //     '100' => 'SUNTRUST BANK NIG LTD',
    //     '215' => 'UNITY BANK PLC',
    // ],

    'banks' => [
        '044' => 'ACCESS BANK PLC',
        '023' => 'CITIBANK NIG LTD',
        '063' => 'DIAMOND BANK PLC',
        '050' => 'ECOBANK NIGERIA PLC',
        '084' => 'ENTERPRISE BANK',
        '070' => 'FIDELITY BANK PLC',
        '011' => 'FIRST BANK OF NIGERIA PLC',
        '214' => 'FIRST CITY MONUMENT BANK PLC',
        '058' => 'GUARANTY TRUST BANK PLC',
        '030' => 'HERITAGE BANK',
        '301' => 'JAIZ BANK PLC',
        '082' => 'KEYSTONE BANK',
        '014' => 'MAINSTREET BANK',
        '076' => 'POLARIS BANK PLC',
        '101' => 'PROVIDUS BANK PLC',
        '221' => 'STANBIC IBTC BANK PLC',
        '068' => 'STANDARD CHARTERED BANK NIGERIA LTD',
        '232' => 'STERLING BANK PLC',
        '100' => 'SUNTRUST BANK NIG LTD',
        '032' => 'UNION BANK OF NIGERIA PLC',
        '033' => 'UBA PLC',
        '215' => 'UNITY BANK PLC',
        '035' => 'WEMA BANK PLC',
        '057' => 'ZENITH BANK PLC',
        '00103'=> 'GLOBUS BANK',
        '50211'=> 'KUDA BANK',
    ],

    'new_implementation'=> '2020-11-01',

    'otp_activation_enabled'=> true, // when there is no otp activation we use manual mandate
    
    
    'banks_with_otp_support' => [
        
        '214' => 'FIRST CITY MONUMENT BANK PLC',
        '030' => 'HERITAGE BANK',
        '076' => 'POLARIS BANK PLC',
        '232' => 'STERLING BANK PLC',
        '100' => 'SUNTRUST BANK NIG LTD',
        '215' => 'UNITY BANK PLC',
        '057' => 'ZENITH BANK PLC',
        '035' => 'WEMA BANK PLC',
        '301' => 'JAIZ BANK PLC',
        '101' => 'PROVIDUS BANK PLC',
        '070' => 'FIDELITY BANK PLC',
    ],

    // Implemeted this for banks whose remita bank code does not tally
    // with that of paystack and the one we currently use on the system
    'remita_specific_bank_code'=> [
        '221'=> ['039', 'STANBIC IBTC BANK PLC'],
        '00103'=> ['103', 'GLOBUS BANK']
    ],

   

];