<?php

return [
    'sharenet_token' => env('SHARENET_TOKEN'),
    
    'company_name' => env('COMPANY_NAME'),

    'new_loans_flag'=>'2019-10-02 15:50:49',
    'deleted_loans_flag'=>'2019-10-29 15:50:49',
    
    'states' => [
        'Abia' => 'Abia',
        'Adamawa' => 'Adamawa',
        'Akwa Ibom' => 'Akwa Ibom',
        'Anambra' => 'Anambra', 
        'Bauchi' => 'Bauchi',
        'Bayelsa' => 'Bayelsa',
        'Benue' => 'Benue',
        'Borno' => 'Borno',
        'Cross River' => 'Cross River',
        'Delta' => 'Delta',
        'Ebonyi' => 'Ebonyi',
        'Enugu' => 'Enugu',
        'Edo' => 'Edo',
        'Ekiti' => 'Ekiti',
        'Gombe' => 'Gombe',
        'Imo' => 'Imo',
        'Jigawa' => 'Jigawa',
        'Kaduna' => 'Kaduna',
        'Kano' => 'Kano',
        'Katsina' => 'Katsina',
        'Kebbi' => 'Kebbi',
        'Kogi' => 'Kogi',
        'Kwara' => 'Kwara',
        'Lagos' => 'Lagos',
        'Nasarawa' => 'Nasarawa',
        'Niger' => 'Niger',
        'Ogun' => 'Ogun',
        'Ondo' => 'Ondo',
        'Osun' => 'Osun',
        'Oyo' => 'Oyo',
        'Plateau' => 'Plateau',
        'Rivers' => 'Rivers',
        'Sokoto' => 'Sokoto',
        'Taraba' => 'Taraba',
        'Yobe' => 'Yobe',
        'Zamfara' => 'Zamfara',
        'FCT' => 'Federal Capital Territory (FCT)'
    ],
    
    'modules' => [
        'phone_verification' => false   
    ],
    
    'asset_flow' => [
        'WTE' => 'wallet-escrow',
        'ETW' => 'escrow-wallet',
        'WTW' => 'wallet-wallet',
        'ETE' => 'escrow-wallet',
        'W' => 'wallet',
        'E' => 'escrow',
        'V'=>'vault',
        'WTV'=>'wallet-vault',
        'VTW'=>'vault-wallet'
    ],
    
    'flow' => [
        'wallet_fund' => '000',
        'withdrawal' => '002',
        'loan_fund' => '003',
        'loan_fund_rvsl' => '004',
        'asset_bid' => '005',
        'asset_bid_rvsl' => '006',
        'asset_bid_approval' => '007',
        'investor_verification' => '008',
        'loan_request' => '009',
        'loan_acceptance' => '010',
        'insurance_fee' => '011',
        'fund_recovery' => '012',
        'fund_recovery_fee' => '013',
        'withdrawal_approval' => '014',
        'withdrawal_rvsl' => '015',
        'corrective_rvsl' => '016',
        'loan_disbursement' => '017',
        'card_set_up_fees' => '018',
        'affiliate_loan_commission' => '019',
        'supervisor_affiliate_commission' => '020',
        'affiliate_verification' => '021',
        'repayment_wallet_recovery'=>'022',
        'loan_transaction'=>'023',
        'loan_fund_recovery'=>'024',
        'tax_payment'=>'025',
        'wallet_vault_transfer'=>'026',
        'portfolio_management_fee'=>'027',
        'investor_loanfund_migration'=>'028',
        'target_bonus_payment'=> '029',
        'vault_to_wallet_auto_sweep'=> '030',
        'zerorise_wallet_balance' => '031',
        'wallet_fund_commission'=> '032',
        'investor_share_affiliate_cost'=> '033',
        'promissory_note_commission'=> '034',
        'promissory_note_interest'=>'035',
        'investor_loanfund_migration_rvsl'=> '041',
        'loan_wallet_transaction'=> '036',
        'investor_upfront_interest' => '043',
        'affiliate_commission_rvsl' => '044',
        'supervisor_commission_rvsl' => '045'
    ],
    
    // description, parties
    // parties - No of accounts involved in transaction
    'flow_codes' => [
        '000' => ['Wallet Fund', 1],
        '001' => ['Payment', 1],
        '002' => ['Withdrawal Request', 1],
        '003' => ['Loan request investment', 2],
        '004' => ['Loan request investment RVSL', 2],
        '005' => ['Asset Bid', 2],
        '006' => ['Asset Bid RVSL', 2],
        '007' => ['Asset Bid Approval', 2],
        '008' => ['Investor Verification Fee', 1],
        '009' => ['Loan Request Fee', 1],
        '010' => ['Loan Funds Release', 2],
        '011' => ['Loan Request Insurance Fee', 1],
        '012' => ['Fund Recovery', 1],
        '013' => ['Fund Recovery Fee', 1],
        '014' => ['Withdrawal Request Approval', 1],
        '015' => ['Withdrawal RVSL', 1],
        '016' => ['Corrective RVSL', 1],
        '017' => ['Loan Disbursement', 1],
        '018' => ['Card Setup Fee', 1],
        '019' => ['Affliate Loan Commission', 1],
        '020' => ['Supervisor Commission from affliate Earning', 1],
        '021' => ['Affiliate Verification Fee', 1],
        '022'=> ['Repayment Wallet Fund Recovery',1],
        '023'=> ['Loan Transaction Fund',1],
        '024'=> ['Loan Fund Recovery For Voided Loans',1],
        '025'=> ['Tax Payment Made ',1],
        '026'=>['Wallet and Vault Transfer',1],
        '027'=>['Portfolio Management Fee For Investors',1],
        '028'=>['Investors LoanFund Migration',2],
        '029'=> ['Target Bonus Payment ', 1],
        '030'=> ['Automatic vault to wallet fund sweep', 1],
        '031'=> ['Investors Zerorise Wallet Balance', 1],
        '032'=> ['Wallet fund Commission', 1],
        '033'=> ['Affiliate Cost of loan', 1],
        '034'=> ['Affiliate Promissory Commission', 1],
        '035'=> ['Promissory Note Interest Payment', 1],
        '041'=>['Investors LoanFund Migration RVSL', 2],
        '036'=> ['Loan Wallet Transaction', 1],
        '043'=> ['Investor Upfront Interest', 1],
        '044'=> ['Affiliate Commission Reversal', 1],
        '045'=> ['Supervisor Commission Reversal', 1]
    ],

    
    
    'collection_plans' => ['DDM', 'DAS'],
    
    'problem_loans' => [
        32, 44, 72, 74, 75, 76, 77, 78, 80, 91, 
        94, 101, 103, 104, 105, 106, 121, 139, 152, 
        187, 195, 216, 233, 235, 263, 310
    ],
    
    'months' => [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ],

    'allowed_origins'=>  [
        'http://nextpayday.ng',
        'https://nextpayday.ng'
    ],

    
];