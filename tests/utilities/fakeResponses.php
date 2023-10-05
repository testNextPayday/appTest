<?php

return [

    'uses-moneysender-test'=> [

        'success-transfer'=> '
        {
          "status":true,
          "message":"Transfer has been queued",
          "data":{
            "integration":100073,
            "domain":"test",
            "amount":3794800,
            "currency":"NGN",
            "source":"balance",
            "reason":"Calm down",
            "recipient":28,
            "status":"success",
            "transfer_code":"TRF_1ptvuv321ahaa7q",
            "reference": "x5j67tnnw1t98k",
            "id":14,
            "createdAt":"2017-02-03T17:21:54.508Z",
            "updatedAt":"2017-02-03T17:21:54.508Z"
          }
        }',

        'pending-transfer' => '{
          "status":true,
          "message":"Transfer has been queued",
          "data":{
          "integration":100073,
          "domain":"test",
          "amount":3794800,
          "currency":"NGN",
          "source":"balance",
          "reason":"Calm down",
          "recipient":28,
          "status":"pending",
          "transfer_code":"TRF_1ptvuv321ahaa7q",
          "reference": "x5j67tnnw1t98k",
          "id":14,
          "createdAt":"2017-02-03T17:21:54.508Z",
          "updatedAt":"2017-02-03T17:21:54.508Z"
          }
          }
        ',

        'failed-transfer' => '{
            "status":true,
            "message":"Transfer has been queued",
            "data":{
            "integration":100073,
            "domain":"test",
            "amount":3794800,
            "currency":"NGN",
            "source":"balance",
            "reason":"Calm down",
            "recipient":28,
            "status":"failed",
            "transfer_code":"TRF_1ptvuv321ahaa7q",
            "reference": "x5j67tnnw1t98k",
            "id":14,
            "createdAt":"2017-02-03T17:21:54.508Z",
            "updatedAt":"2017-02-03T17:21:54.508Z"
            }
          }',

        'success-recipient'=>'
        {
            "status": true,
            "message": "Recipient created",
            "data": {
              "type": "nuban",
              "name": "Zombie",
              "description": "Zombier",
              "metadata": {
                "job": "Flesh Eater"
              },
              "domain": "test",
              "details": {
                "account_number": "0100000010",
                "account_name": null,
                "bank_code": "044",
                "bank_name": "Access Bank"
              },
              "currency": "NGN",
              "recipient_code": "RCP_1i2k27vk4suemug",
              "active": true,
              "reference": "x5j67tnnw1t98k",
              "id": 27,
              "createdAt": "2017-02-02T19:35:33.686Z",
              "updatedAt": "2017-02-02T19:35:33.686Z"
            }
          }',

          'verify-transfer'=>'{
            "status": true,
            "message": "Transfer retrieved",
            "data": {
              "recipient": {
                "domain": "test",
                "type": "nuban",
                "currency": "NGN",
                "name": "Flesh",
                "details": {
                  "account_number": "olounje",
                  "account_name": null,
                  "bank_code": "044",
                  "bank_name": "Access Bank"
                },
                "metadata": null,
                "recipient_code": "RCP_2x5j67tnnw1t98k",
                "active": true,
                "id": 28,
                "integration": 100073,
                "createdAt": "2017-02-02T19:39:04.000Z",
                "updatedAt": "2017-02-02T19:39:04.000Z"
              },
              "domain": "test",
              "amount": 4400,
              "currency": "NGN",
              "source": "balance",
              "source_details": null,
              "reason": "Redemption",
              "status": "success",
              "reference": "x5j67tnnw1t98k",
              "failures": null,
              "transfer_code": "TRF_2x5j67tnnw1t98k",
              "id": 14938,
              "createdAt": "2017-02-03T17:21:54.000Z",
              "updatedAt": "2017-02-03T17:21:54.000Z"
            }
          }'
        ],

        'uses-moneygram-test'=> [

          'success-transfer' => '{
            "message": "Transfer has been queued",
            "status" : "success",
            "transfer_code": "TRF_2x5j67tnnw1t98k",
            "amount" : 300
          }',

          'failed-transfer' => '{
            "message": "Transfer has been queued",
            "status" : "failed",
            "transfer_code": "TRF_2x5j67tnnw1t98k",
            "amount" : 300
          }'
        ],

        'paystack-charge-test'=> [

          'success'=>'{
            "status":true,
            "message":"Verification successful",
            "data":{
                "id": 2345,
                "amount":27000,
                "currency":"NGN",
                "transaction_date":"2016-10-01T11:03:09.000Z",
                "status":"success",
                "reference":"DG4uishudoq90LD",
                "domain":"test",
                "metadata":{},
                "gateway_response":"Successful",
                "channel":"card",
                "ip_address":"41.1.25.1",
                "log":{},
                "authorization":{
                  "authorization_code": "AUTH_8dfhjjdt",
                  "card_type": "visa",
                  "last4": "1381",
                  "exp_month": "06",
                  "exp_year": "2021",
                  "bin": "412345",
                  "brand" : "visa",
                  "bank": "TEST BANK",
                  "channel": "card",
                  "signature": "SIG_idyuhgd87dUYSHO92D",
                  "reusable": true,
                  "country_code": "NG",
                  "account_name": "BoJack Horseman"
                },
                "customer":{},
                "plan":"PLN_0as2m9n02cl0kp6"
              }
            }',

            'refund'=>'{
              "status":true,
              "message":"Refund created",
              "data":{
                  "id": 2345,
                  "amount":27000,
                  "currency":"NGN",
                  "transaction_date":"2016-10-01T11:03:09.000Z",
                  "status":"reversed",
                  "reference":"DG4uishudoq90LD",
                  "domain":"test",
                  "metadata":{},
                  "gateway_response":"Successful",
                  "channel":"card",
                  "ip_address":"41.1.25.1",
                  "log":{},
                  "authorization":{},
                  "customer":{},
                  "plan":"PLN_0as2m9n02cl0kp6"
                }
              }',

            'failed'=>'{
              "status":true,
              "message":"The card was stolen",
              "data":{
                  "id": 2345,
                  "amount":27000,
                  "currency":"NGN",
                  "transaction_date":"2016-10-01T11:03:09.000Z",
                  "status":"failed",
                  "reference":"DG4uishudoq90LD",
                  "domain":"test",
                  "metadata":{},
                  "gateway_response":"Stolen card",
                  "channel":"card",
                  "ip_address":"41.1.25.1",
                  "log":{},
                  "authorization":{},
                  "customer":{},
                  "plan":"PLN_0as2m9n02cl0kp6"
                }
              }'
        ]

];
?>