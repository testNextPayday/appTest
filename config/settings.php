<?php

return [
    'support_mail' => 'support@unicredit.ng',
    
    'collection_methods' => [
        
        'ddm' => [
            '100' => 'Remita',
            '101' => 'Okra',
            '102' => 'Mono',
            '103' => 'Lydia',
        ],
        
        'das' => [
            '200' => 'Remita',
            '201' => 'IPPIS',
            '202' => 'RVSG',
            //'203'=>'WiseTrader'
        ],
        
        'card' => [
            '300' => 'Paystack'  
        ],

        'Default'=> [
            '400'=> 'GSI',            
        ],

        'WiseTrader'=>[
            '500'=>'WiseTrader'
        ]
    ],
    
    'entity_codes' => [
        '001' => 'App\Models\Admin',
        '002' => 'App\Models\Investor',
        '003' => 'App\Models\Staff',
        '004' => 'App\Models\Affiliate',
        '005' => 'App\Models\User'
    ],

    'notification_code'=> [

        'ticket'=> [
            'permissions'=> ['manage_support'],
        ]
    ],

    'salary_plans' => [
        '20' => 'Starter',
        '40' => 'Basic',
        '60' => 'Standard',
        '80' => 'Classic',
        '100' => 'Premium'
    ]



    
    
];