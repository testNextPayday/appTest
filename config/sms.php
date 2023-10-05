<?php

return [

    'termii' => [

    	'apiToken'=>env('TERMII_API_TOKEN'),

    	'baseUrl' =>env('TERMII_BASE_URL'),

    	'senderId' =>env('TERMII_SENDER_ID'),

		'sender'=>env('TERMII_SENDER', 'Nextpayday'),
		
		'multichannelUrl'=>env('TERMII_API_MULTICHANNEL'),

		'numberApiUrl'=>env('TERMII_NUMBER_API')
    
    ]
];
?>