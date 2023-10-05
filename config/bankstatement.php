<?php


return [

    /**
     * Client ID from MyBankStatement
     *
     */
    'clientID' => getenv('MY_BANKSTATEMENT_API_CLIENT_ID'),

    'corporateEmail'=> getenv('MY_BANKSTATEMENT_EMAIL'),

    /**
     * Client Secret From MyBankStatement
     *
     */
    'clientSecret' => getenv('MY_BANKSTATEMENT_API_CLIENT_SECRET'),

    /**
     * MyBankStatement Base Url
     *
     */
    'baseUrl' => getenv('MY_BANKSTATEMENT_API_BASE_URL'),

    'banks'=> [

          "044"=> [
            'id' => 6,
            'name' => 'Access Bank',
            'sortCode' => '044',
         ],

          "050"=>[
            'id' => 32,
            'name' => 'Eco Bank',
            'sortCode' => '050',
          ],

          "214"=>[
            'id' => 5,
            'name' => 'FCMB',
            'sortCode' => '214',
          ],

          "070"=>[
            'id' => 15,
            'name' => 'Fidelity Bank',
            'sortCode' => '070',
          ],

          "030"=> [
            'id' => 7,
            'name' => 'Heritage Bank',
            'sortCode' => '030',
          ],

         "076"=>[
            'id' => 2,
            'name' => 'Polaris Bank Limited',
            'sortCode' => '076',
         ],

          "101"=>[
            'id' => 37,
            'name' => 'Providus Bank',
            'sortCode' => '000',
          ],
          "221"=>[
            'id' => 10,
            'name' => 'Stanbic IBTC Bank',
            'sortCode' => '221',
            ],

          "232"=>[
            'id' => 1,
            'name' => 'Sterling Bank',
            'sortCode' => '232',
            ],

          "033"=>[
            'id' => 14,
            'name' => 'UBA ',
            'sortCode' => '033',
          ],
          "032"=>[
            'id' => 11,
            'name' => 'Union Bank',
            'sortCode' => '032',
          ],
          "035"=>[
            'id' => 12,
            'name' => 'Wema Bank',
            'sortCode' => '035',
          ],

          "011"=>[
            'id' => 3,
            'name' => 'First Bank',
            'sortCode' => '011',
          ],

          "058"=>[
            'id' => 13,
            'name' => 'GT Bank',
            'sortCode' => '058',
          ],

          "082"=>[
            'id' => 4,
            'name' => 'Keystone Bank',
            'sortCode' => '082',
          ],          

          "057"=>[
            'id' => 17,
            'name' => 'Zenith Bank',
            'sortCode' => '057',
          ],

        ]

];
