<?php


Route::get('/invitation/register/{email}/{code}', 'Auth\RegisterController@showInviteRegistrationForm')->name('staff.invitation.register');
Route::post('/invitation/register', 'Auth\RegisterController@inviteRegister')->name('staff.invitation.register.submit');
Route::view('register', 'auth.staff_register');
Route::post('/register', 'Auth\RegisterController@createStaff')->name('staff.register');
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('staff.login');
Route::post('/login', 'Auth\LoginController@login')->name('staff.login.submit');
Route::get('logout/', 'Auth\LoginController@logout')->name('staff.logout');

Route::view('password-forgot', 'auth.staff.passwords.email')->name('staff.passwords.forgot');
Route::post('password-forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('staff.passwords.reset');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('staff.passwords.request');

Route::group(['middleware' => 'auth:staff'], function() {

    Route::get('/', 'StaffController@dashboard')->name('staff.dashboard');

    Route::group(['prefix' => 'repayment'],function(){

       Route::get('/','Repayments\LoanRepaymentController@index')->name('staff.show.repayments')->middleware('staff.roles:repayments');
       Route::post('/confirm/{id}','Repayments\LoanRepaymentController@confirm')->name('staff.repayment.confirm')->middleware('staff.roles:repayments');
       Route::post('/unconfirm/{id}','Repayments\LoanRepaymentController@unconfirm')->name('staff.repayment.unconfirm')->middleware('staff.roles:repayments');
       Route::get('/pending/approval','Repayments\LoanRepaymentController@pendingApproval')->name('staff.pending.repayments')->middleware('staff.roles:repayments');
      // Route::get('/approve','Repayments\LoanRepaymentController@approve')->name('staff.approve.repayments')->middleware('staff.roles:repayments');
       Route::get('/pending/approval','Repayments\LoanRepaymentController@pendingApproval')->name('staff.pending.repayments')->middleware('staff.roles:repayments');
       Route::get('/approve','Repayments\LoanRepaymentController@approve')->name('staff.approve.repayments')->middleware('staff.roles:approve_repayment');
       Route::post('/approve','Repayments\LoanRepaymentController@approveAll')->name('staff.approve.all.repayments')->middleware('staff.roles:repayments');
       Route::post('/delete','Repayments\LoanRepaymentController@deleteAll')->name('staff.delete.all.repayments')->middleware('staff.roles:repayments');
       Route::get('/bulk','Repayments\LoanRepaymentController@bulk')->name('staff.bulk.repayments')->middleware('staff.roles:repayments');
       Route::post('/bulk','Repayments\LoanRepaymentController@bulkRepayment')->name('staff.bulk-repayment')->middleware('staff.roles:repayments');
       Route::get('/bulk/borrowers','Repayments\LoanRepaymentController@getBorrowers')->middleware('staff.roles:repayments')->middleware('staff.roles:repayments');
       Route::delete('/delete/{id}','Repayments\LoanRepaymentController@delete')->name('staff.repayment.delete');
       Route::post('/delete','Repayments\LoanRepaymentController@deleteAll')->name('staff.delete.all.repayments');
       
    });
    Route::group(['prefix' => 'accounts'], function() {
       Route::get('/', 'UserController@index')->name('staff.accounts.index'); 
       Route::get('/borrowers', 'UserController@borrowers')->name('staff.accounts.borrowers')->middleware('staff.roles:borrowers'); 
       Route::get('/new', 'UserController@new')->name('staff.accounts.new'); 
       Route::get('/view/{user}', 'UserController@view')->name('staff.accounts.view');
       Route::post('/store', 'UserController@store')->name('staff.accounts.store'); 
       Route::post('/storev2', 'UserController@storeJSON')->name('staff.accounts.storeV2');
       Route::post('/upload-docs', 'UserController@uploadDocuments')->name('staff.accounts.uploadDocs');
       Route::get('/investors', 'InvestorController@index')->name('staff.accounts.investors');
       Route::get('/investors/{investor}', 'InvestorController@view')->name('staff.accounts.investors.view');
       Route::get('/upgrade/{reference}', 'UserController@upgrade')->name('staff.accounts.upgrade')->middleware('staff.roles:investors'); 
       Route::post('user/upgrade', 'UserController@upgradeSalaryPercentage')->name('staff.users.upgrade')->middleware('staff.roles:upgrade_user');
    });

    Route::group(['prefix'=>'promissory-notes', 'middleware'=>'staff.roles:promissory_notes'], function() {

        Route::get('/new', 'PromissoryController@create')->name('staff.promissory-notes.create');
     
        Route::post('/store', 'PromissoryController@store')->name('staff.promissory-notes.store');
     
        Route::get('/index', 'PromissoryController@index')->name('staff.promissory-notes.index');
     
        Route::get('/bank', 'PromissoryController@investorBank')->name('staff.promissory-notes.bank');
     
        Route::post('/bank/store', 'PromissoryController@investorBankStore')->name('staff.promissory-notes.bank-store');
     
        Route::get('/view/{promissory_note}', 'PromissoryController@view')->name('staff.promissory-notes.view');
     });

    
    Route::post('employments/{employment}', 'EmploymentController@update')->name('staff.employments.update');
    Route::post('employments/update/payroll/{employment}', 'EmploymentController@updatePayRoll')->name('staff.employments.update_payroll');
    
    Route::group(['prefix' => 'employers'], function() {
       Route::post('/add', 'EmployerController@store')->name('staff.employers.add'); 
    });

    Route::get('/disburse/{loan}', 'LoanRequestDisbursementController@disburseLoan')->name('staff.loans.disburse')->middleware('staff.roles:loan_disbursement');
    // This route is not being utilized only admin can dissolve loan as it is
    Route::post('/dissolve/loan/{reference}', 'LoanController@dissolveLoan')->name('staff.loans.dissolveloan');

    Route::group(['prefix' => 'funds', 'middleware' => 'staff.roles:investors'], function() {
        Route::get('/', 'LoanFundController@index')->name('staff.funds.index'); 
        Route::get('/view/{loan_id?}', 'LoanFundController@viewFund')->name('staff.funds.view');
        Route::get('/acquired', 'LoanFundController@acquired')->name('staff.funds.acquired');
        Route::get('/acquired/view/{id?}', 'LoanFundController@viewAcquiredLoan')->name('staff.funds.acquired.view');
        Route::post('/transfer/place', 'LoanFundController@placeOnTransfer')->name('staff.funds.transfer');
    });

    Route::group(['prefix'=>'loan_transaction','middleware'=>'staff.roles:loan_transactions'],function(){
        Route::post('/add/{reference}','LoanTransactionController@add')->name('staff.loan.transaction.add');
     });
    
    Route::group(['prefix' => 'loan-requests', 'middleware' => 'staff.roles:borrowers'], function() {
        Route::get('/', 'LoanRequestController@index')->name('staff.loan-requests.index');  
        Route::get('/view/{reference?}', 'LoanRequestController@view')->name('staff.loan-requests.view');
        Route::get('/cancel/{reference?}', 'LoanRequestController@cancel')->name('staff.loan-requests.cancel');
        Route::get('/create', 'LoanRequestController@create')->name('staff.loan-requests.create');
        Route::get('/users', 'LoanRequestController@getBorrowers')->name('staff.loan-requests.users');
        Route::get('/getBorrowers', 'LoanRequestController@getBorrowers')->name('staff.loan-requests.borrowers');
        
        Route::get('/store/pay', 'LoanRequestController@handleApplicationPaymentResponse')->name('staff.loan-requests.store.pay');
        Route::post('/store', 'LoanRequestController@store')->name('staff.loan-requests.store');
        
        Route::get('/mandate/check/{loanRequest?}', 'LoanRequestController@checkLoanRequestMandateStatus')->name('staff.loan-request.mandate.check');
        // Route::get('/accept-loan/{reference}', 'LoanRequestController@acceptLoan')->name('staff.loan-requests.accept');
        Route::get('/accept-funds/{loanRequest?}', 'LoanRequestController@accept')->name('staff.loan-requests.accept-funds');
        
        Route::get('/max-amount/{reference?}/{duration?}/{employment?}', 'LoanRequestController@checkMaxRequestAmount')->name('staff.loan-requests.checkmax');
        Route::get('/emi-amount/{duration?}/{employment?}/{amount?}', 'LoanRequestController@checkMonthlyRepayment')->name('staff.loan-requests.checkemi');
    });



    Route::group(['prefix'=>'loan-requests','middleware'=>'staff.roles:loan_request_setup'],function(){
        Route::post('prepare/{loanRequest?}', 'LoanRequestDisbursementController@prepareLoan')->name('staff.loan-requests.prepare-loan');
        Route::get('/das-eligibility/{user?}', 'UserController@viewSalaryInfo')->name('staff.users.das');
        Route::get('/pending-setup', 'LoanRequestController@pendingSetup')->name('staff.loan-requests.pending-setup');
    });

    Route::group(['prefix'=>'loan-requests','middleware'=>'staff.roles:loan_request'],function(){
       // Route::post('prepare/{loanRequest}', 'LoanRequestDisbursementController@prepareLoan')->name('staff.loan-requests.prepare-loan');
        Route::get('/pending', 'LoanRequestController@pending')->name('staff.loan-requests.pending');
    });

    
    Route::group(['prefix' => 'loan-requests', 'middleware' => 'staff.roles:investors'], function() {
        Route::get('/active', 'LoanRequestController@active')->name('staff.loan-requests.active');
        Route::group(['prefix' => 'funds'], function() {
            Route::post('/place', 'LoanFundController@fundLoan')->name('staff.loan-requests-place-fund');
        });
    });

    Route::group(['prefix'=>'settlement','middleware'=>'staff.roles:settlements'],function(){

        Route::get('/all','SettlementController@index')->name('staff.show.settlements');
        Route::get('/add','SettlementController@new')->name('staff.add.settlements');
        Route::get('/view/{reference?}','SettlementController@view')->name('staff.settlement.view');
        Route::post('/upload','SettlementController@uploadSettlement')->name('staff.upload.settlement');
        Route::get('settlement/preview/{reference?}','SettlementController@previewDoc')->name('staff.settlement.preview');
        Route::post('/settlement/payment/loan/{reference?}','SettlementController@pay')->name('staff.pay.settlement');
        Route::get('/settlement/payment/callback','SettlementController@handleSettlementPaymentCallback')->name('staff.settlement.payment_callback');
        Route::get('/confirm/{reference}','Settlement\SettlementController@confirm')->name('staff.settlement.confirm');
   Route::post('/decline/{reference}','Settlement\SettlementController@decline')->name('staff.settlement.decline');
   Route::post('/unconfirm/{reference}','Settlement\SettlementController@unconfirm')->name('staff.settlement.unconfirm');
   Route::get('/invoice/{reference}','Settlement\SettlementController@settlementInvoice')->name('staff.invoice.view');
    });
    
    Route::group(['prefix' => 'loans', 'middleware' => 'staff.roles:borrowers'], function() {
       Route::get('/active', 'LoanController@activeLoans')->name('staff.loans.active'); 
       Route::get('/eligible-top-up','LoanController@eligibleTopUp')->name('staff.loans.eligible');
       Route::get('/received', 'LoanController@receivedLoans')->name('staff.loans.received'); 
       Route::get('/given', 'LoanController@givenLoans')->name('staff.loans.given'); 
       Route::get('/given/view/{id}', 'LoanController@viewGivenLoan')->name('staff.loans.given.view');
       Route::get('/acquired', 'LoanController@acquiredLoans')->name('staff.loans.acquired'); 
       Route::get('/acquired/view/{id}', 'LoanController@viewAcquiredLoan')->name('staff.loans.acquired.view');
       Route::get('/purchase/available', 'LoanController@getAvailableLoanPurchases')->name('staff.loans.purchase.available');
       Route::get('/fulfilled','LoanController@fulfilledLoans')->name('staff.loans.fulfilled');
       Route::get('/pending', 'LoanController@pendingLoans')->name('staff.loans.pending');
       Route::get('/', 'LoanController@index')->name('staff.loans.index');
       Route::get('/view/{reference}', 'LoanController@view')->name('staff.loans.view');
       Route::get('/mandates/authority-form/{loan}/{type?}', 'MandateController@getAuthorityForm')->name('staff.loans.authorityForm');
        Route::post('/mandates/authority-form/{loan}', 'MandateController@uploadAuthorityForm')->name('staff.loans.authorityForm.upload');
    });
    Route::group(['prefix' => 'repayments', 'middleware' => 'staff.roles:borrowers'], function() {
         Route::get('/new/{user}/{reference}','Repayments\LoanController@index')->name('staff.repayment');
         Route::post('/new/{reference}','Repayments\LoanController@store')->name('staff.repayment.store');
         Route::get('/all/{reference}','Repayments\LoanController@showAll')->name('staff.repayments');
         Route::delete('/all/{id}','Repayments\LoanController@delete')->name('staff.repayment.delete');
         Route::get('/pay/repayment/callback','Repayments\LoanController@handleGatewayCallback')->name('staff.paystack.payment');
        
    });

    Route::group(['prefix'=>'loans','middleware'=>'staff.roles:sweeps'],function(){

        Route::post('/sweep-loan/{loan}', 'LoanSweeperController@sweepLoan')->name('staff.loans.sweep-single');
    });
    Route::group(['prefix' => 'loan-transfers', 'middleware' => 'staff.roles:investors'], function() {
        Route::get('/', 'LoanTransferController@index')->name('staff.loan-transfers.index'); 
    });
    
    Route::group(['prefix' => 'bids', 'middleware' => 'staff.roles:investors'], function() {
        Route::get('/', 'BidController@index')->name('staff.bids.index');
        Route::get('/cancel', 'BidController@cancel')->name('staff.bids.cancel');
        Route::group(['prefix' => 'loans'], function() {
            Route::post('place', 'LoanTransferController@placeBid');    
            Route::post('update', 'LoanTransferController@updateBid')->name('staff.bids.loans.update');    
            Route::get('accept/{loan_id}', 'LoanTransferController@acceptBid')->name('staff.bids.loans.accept');    
        });
    });

    Route::group(['prefix' => 'managed/loans', 'middleware' => 'staff.roles:loan_restructuring'], function() {
        Route::get('/', 'LoanController@managed')->name('staff.loan-managed.index');
    });

    Route::group(['prefix'=>'bills'], function() {
           Route::get('/manage', 'BillsController@index')->name('staff.bills.manage')->middleware('staff.roles:bills');
           Route::get('/pending', 'BillsController@pending')->name('staff.bills.pending')->middleware('staff.roles:approve_bills');
    });

    Route::group(['prefix'=>'group', 'middleware' => 'staff.roles:borrowers_group'], function() {
        Route::get('/add', 'BorrowerGroupController@getActiveLoans')->name('staff.group.create');
        Route::post('/create', 'BorrowerGroupController@storeGroupedBorrowers')->name('staff.group.store');
        Route::get('/view', 'BorrowerGroupController@viewGroups')->name('staff.group.view');
        Route::get('/view/{reference}', 'BorrowerGroupController@viewSingleGroup')->name('staff.group.single-view');

        Route::get('/search', 'BorrowerGroupController@search')->name('staff.group.search');
        Route::get('/index', 'BorrowerGroupController@index')->name('staff.group.index');
    });


    Route::group(['prefix'=>'salary-management', 'middleware'=>'staff.roles:salary_payment'], function() {
        Route::get('/index', 'SalaryController@index')->name('staff.salary-payment');
    });
    
    
    Route::group(['prefix' => 'profile'], function() {
        Route::get('/', 'ProfileController@index')->name('staff.profile.index'); 
        Route::post('/update', 'ProfileController@update')->name('staff.profile.update'); 
        Route::post('/update/bank', 'ProfileController@updateBank')->name('staff.profile.update-bank'); 
    });
    
    Route::group(['prefix' => 'investors', 'middleware' => 'staff.roles:investors'], function() {
        Route::get('/create', 'InvestorController@create')->name('staff.investors.create');
        Route::post('/create', 'InvestorController@store');
        Route::get('/apply/{reference?}', 'InvestorController@showApplication')->name('staff.investors.apply');
        Route::post('/apply', 'InvestorController@apply')->name('staff.investors.submit-application');
    });

    Route::group(['prefix' => 'refund'], function(){
        Route::post('/store', 'RefundController@store')->name('staff.create.refund');
        Route::get('/logs', 'RefundController@view')->name('staff.refund.logs');
        Route::get('/pending', 'RefundController@pendingRefund')->name('staff.refund.pending')->middleware('staff.roles:approve_refunds');
        Route::patch('/update/{id}/{status}/', 'RefundController@update')->name('staff.refund.update')->middleware('staff.roles:approve_refunds');
    });

    Route::group(['prefix'=> 'withdrawal-requests', 'middleware'=> 'staff.roles:withdrawal_approval'], function(){
        Route::get('/pending', 'WithdrawalRequestController@pending')->name('staff.withdrawals.pending');
        Route::get('/approved', 'WithdrawalRequestController@approved')->name('staff.withdrawals.approved');
        Route::get('/declined', 'WithdrawalRequestController@declined')->name('staff.withdrawals.declined');
        Route::get('/approve/{request_id}', 'WithdrawalRequestController@approve')->name('staff.withdrawals.approve');
        Route::get('/approve-backend/{request_id}', 'WithdrawalRequestController@approveBackend')->name('staff.withdrawals.approve-backend');
        Route::get('/decline/{request_id}', 'WithdrawalRequestController@decline')->name('staff.withdrawals.decline');
    });

   

    Route::group(['prefix'=> 'tickets', 'middleware'=>'staff.roles:support'], function() {

        Route::get('/ticket/show/{ticket}', 'TicketController@show')->name('staff.ticket.show');
        Route::get('/', 'TicketController@active')->name('staff.ticket.active');
        Route::post('/respond/{ticket}', 'TicketController@respond')->name('staff.ticket.respond');
        Route::post('/close/ticket/{ticket}', 'TicketController@close')->name('staff.ticket.close');
    });

    Route::group(['prefix'=> 'followup'], function() {

        Route::get('/users', 'FollowupController@users')->name('staff.followup.users')->middleware('staff.roles:followup_users');
        Route::get('/takeup/users/{user}', 'FollowupController@takeUp')->name('staff.takeup.users')->middleware('staff.roles:followup_users');
        Route::get('/investors', 'FollowupController@investors')->name('staff.followup.investors')->middleware('staff.roles:followup_investors');

    });

    Route::group(['prefix'=>'reports'],function(){
        Route::get('/','ReportController@index')->name('staff.report.view')->middleware('staff.roles:view_report');
     });

     Route::get('/get-all-affiliates', 'LoanRequestController@getAffiliates');


     Route::group(['prefix'=> 'withdrawal-requests', 'middleware'=> 'staff.roles:withdrawal_approval'], function(){
        Route::get('/pending', 'WithdrawalRequestController@pending')->name('staff.withdrawals.pending');
        Route::get('/approved', 'WithdrawalRequestController@approved')->name('staff.withdrawals.approved');
        Route::get('/declined', 'WithdrawalRequestController@declined')->name('staff.withdrawals.declined');
        Route::get('/approve/{request_id}', 'WithdrawalRequestController@approve')->name('staff.withdrawals.approve');
    });

    
});
