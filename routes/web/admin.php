<?php

/** Broadcast route for admins **/
Route::post('/auth/broadcasting', function(Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
})->middleware('auth:admin');

Route::group(['prefix'=>'wallet'], function () {
   
   Route::get(
      '/reports', 'WalletTransactionController@index'
   )->name('admin.wallet.reports');

});

Route::group(['prefix'=>'managed-loans-sweeps'], function () {
   
   Route::get(
      '/view', 'ManagedLoanSweeperController@index'
   )->name('admin.loans.get-managed-sweepable');
   
   Route::get(
      '/', 'ManagedLoanSweeperController@getPlans'
   )->name('admin.loans.managed-sweepable');

   Route::post(
      '/plan/{plan}', 'ManagedLoanSweeperController@sweepPlan'
   )->name('admin.loans.managed.sweep-plan');

   Route::get(
      '/status', 'ManagedLoanSweeperController@getStatus'
   )->name('admin.managed.sweep-status');
});

Route::group(['prefix'=> 'collection/insights'], function() {

   Route::get('/success/rate', 'Collection\CollectionInsightDataController@successRates')->name('admin.success-rate.data');
   Route::get('/collection/intervals', 'Collection\CollectionInsightDataController@collectionIntervals')->name('admin.collection-intervals.data');
   Route::get('/collection/intervals/data', 'Collection\CollectionInsightDataController@collectionPlans')->name('admin.collection-intervals.plans');
});

Route::group(
   ['prefix'=> 'loan-sweeper'], function() {

      Route::get('card/loans/active', 'LoanSweeperController@loansWithCard');
   }
);
Route::group(['prefix'=>'payment-salaries'],function(){

   Route::get('/','SalaryPaymentController@index')->name('admin.payment.salaries');

   Route::get('/staffs','SalaryPaymentController@getStaffs')->name('admin.payment.salaries-data');

   Route::post('/update/{staff}','SalaryPaymentController@updateSalary')->name('admin.payment.update-salary');

   Route::post('/pay/staff/{staff}','SalaryPaymentController@paySingleStaff')->name('admin.payment.pay-staff-single');

   Route::post('/pay/staffs/bulk','SalaryPaymentController@payStaffsInBulk')->name('admin.payment.pay-stafs-bulk');

});

Route::post('/wallet-transactions/cancel/{wallettransaction}', 'WalletTransactionController@cancel');

Route::group(['prefix'=>'gateway-transactions'],function(){

   Route::get('/','GatewayTransactionController@index')->name('admin.payment.transactions');

   Route::get('/failed','GatewayTransactionController@failed')->name('admin.failed.transactions');

   Route::get('/get/{gateway}','GatewayTransactionController@getTransaction')->name('admin.payment.transactions-get');

   Route::get('/linked-data/{gateway}','GatewayTransactionController@getLinkedData')->name('admin.payment.transactions.linked-data');

   Route::get('/data','GatewayTransactionController@getRecords')->name('admin.payment.records');

   Route::post('/retry/{gateway}','GatewayTransactionController@retryTransaction')->name('admin.payment.retry');

   Route::post('/new/{gateway}','GatewayTransactionController@newTransaction')->name('admin.payment.new');

   Route::post('/resend-otp/{gateway}','GatewayTransactionController@resendOtp')->name('admin.payment.resend-otp');

   Route::post('/finalize/{gateway}','GatewayTransactionController@finalizeTransaction')->name('admin.payment.finalize');

   Route::post('/verify/{gateway}','GatewayTransactionController@verifyTransaction')->name('admin.payment.verify');

});

Route::group(['prefix'=>'transfer-controls'], function(){

   Route::get('/','TransferControlsController@index')->name('admin.payment.controls');

   Route::get('/balance/history','TransferControlsController@getGatewayBalanceHistory')->name('admin.payments.balance-history');

   Route::get('/check/balance','TransferControlsController@checkGatewayPaymentBalance')->name('admin.payments.check-balance');

   Route::post('/disable/otp','TransferControlsController@disableOtpRequirement')->name('admin.payments.disable-otp');

   Route::post('/enable/otp','TransferControlsController@enableOtpRequirement')->name('admin.payments.enable-otp');

   Route::post('/final/disable/otp','TransferControlsController@finalDisableOtpRequirement')->name('admin.payments.final-disable-otp');

   // route explicit model binding in routeserviceprovider
   Route::post('/create/recipient/{bankdetail}','TransferControlsController@createRecipient')->name('admin.recipient.create');

});

Route::group(['prefix'=>'promissory-notes'], function() {

   Route::get('/new', 'PromissoryController@create')->name('admin.promissory-notes.create');

   Route::post('/store', 'PromissoryController@store')->name('admin.promissory-notes.store');

   Route::get('/index', 'PromissoryController@index')->name('admin.promissory-notes.index');

   Route::get('/active', 'PromissoryController@active')->name('admin.promissory-notes.active');

   Route::get('/pending', 'PromissoryController@pending')->name('admin.promissory-notes.pending');

   Route::get('/bank', 'PromissoryController@investorBank')->name('admin.promissory-notes.bank');

   Route::post('/bank/store', 'PromissoryController@investorBankStore')->name('admin.promissory-notes.bank-store');

   Route::get('/view/{promissory_note?}', 'PromissoryController@view')->name('admin.promissory-notes.view');

   Route::post('/approve/{promissory_note?}', 'PromissoryController@approve')->name('admin.promissory-notes.approve');

   Route::post('/liquidate/{promissory_note?}', 'PromissoryController@liquidate')->name('admin.promissory-notes.liquidate');

   Route::post('/rollover/{promissory_note?}', 'PromissoryController@rollover')->name('admin.promissory-notes.rollover');

   Route::post('/withdraw/{promissory_note?}', 'PromissoryController@withdraw')->name('admin.promissory-notes.withdraw');

   Route::post('/update/{promissory_note?}', 'PromissoryController@update')->name('admin.promissory-notes.update');

   Route::post('/delete/{promissory_note?}', 'PromissoryController@delete')->name('admin.promissory-notes.delete');

   Route::group(['prefix'=> 'settings'], function() {
      Route::get('/', 'PromissoryNoteSettingController@index')->name('admin.promissory-settings.index');
      Route::post('/store', 'PromissoryNoteSettingController@store')->name('admin.promissory-settings.store');
      Route::post('/update/{note_setting}', 'PromissoryNoteSettingController@update')->name('admin.promissory-settings.update');
      Route::post('/delete/{note_setting}', 'PromissoryNoteSettingController@destroy')->name('admin.promissory-settings.delete');
   });   
});

Route::group(['prefix'=> 'paydaynotes'], function() {
   Route::get('/all-payments', 'PaydayNoteInvestors\PaymentController@allPayment')->name('admin.promissory-notes.all.payments');
   Route::get('/pending-payments', 'PaydayNoteInvestors\PaymentController@pendingPayment')->name('admin.promissory-notes.pending.payments');
   Route::get('/approved-payments', 'PaydayNoteInvestors\PaymentController@approvedPayment')->name('admin.promissory-notes.approved.payments');
   Route::post('/verify/mono', 'PaydayNoteInvestors\PaymentController@verifyMonoStatus')->name('admin.promissory-note.verifymonostatus');
});

Route::group(['prefix'=> 'birthdays'], function() {

   Route::get('/', 'BirthdayController@index')->name('admin.birthdays.index');

   Route::get('/today', 'BirthdayController@birthdaysToday');

   Route::get('/tomorrow', 'BirthdayController@birthdaysTomorrow');

   Route::get('/search', 'BirthdayController@birthdaySearch');
});

Route::group(['prefix'=>'bills'],function(){

   Route::get('/','BillsController@index')->name('admin.bills.index');

   Route::get('/statistics','BillsController@statistics')->name('admin.bills.stats');

   Route::get('/statistics/data','BillsController@statisticsData')->name('admin.bills.stats-data');

   Route::get('/banks','BillsController@getBanks')->name('admin.bills.banks');

   Route::post('/store','BillsController@store')->name('admin.bills.store');

   Route::post('/category/store', 'BillCategoryController@addCategory');

   Route::post('/category/delete', 'BillCategoryController@deleteCategories');

   Route::post('/category/updates', 'BillCategoryController@updateCategories');

   Route::get('/category/all', 'BillCategoryController@index');

   Route::post('/pay/active/bills','BillsController@payActiveBills')->name('admin.bills.pay-active');

   Route::post('/single/pay/{bill}','BillsController@paySingleBill')->name('admin.bills.pay-single');

   Route::put('/update/{bill}','BillsController@update')->name('admin.bills.update');

   Route::delete('/delete/{bill}','BillsController@delete')->name('admin.bills.delete');

   Route::get('/data','BillsController@getRecords')->name('admin.bills.records');

   Route::get('/transactions/{bill}','BillsController@getBillTransactions')->name('admin.bills.transactions');

   Route::get('/pending','BillsController@pendingRequests')->name('admin.bills.pending');

   Route::get('/declined','BillsController@declinedRequests')->name('admin.bills.declined');

   Route::post('/cancel/{bill_request}','BillsController@declineRequest')->name('admin.bills.reject');

   Route::post('/request/payment/{bill}','BillsController@requestPayment')->name('admin.bills.request-payment');

});

Route::group(['prefix' => 'loans'], function() {

   Route::get('/view/{reference?}', 'LoanController@view')->name('admin.loans.view');
   Route::post('/back-date-collected-date', 'LoanController@backDateCollectionDate')->name('admin.loans.back_date');
   Route::post('/back-date-due-days', 'LoanController@backDateDueDate')->name('admin.loans.back_date_due_date');
   Route::post('/back-date-single-due-date', 'LoanController@changeSingleDueDate')->name('admin.loans.change_single_due_date');
   Route::get('/back-date-pay-dates/{loan_id}/{type}', 'LoanController@backDatePayDays')->name('admin.loans.back_date_paydays');

   Route::get('/active', 'LoanController@activeLoans')->name('admin.loans.active'); 
   Route::get('/auto-sweeping', 'LoanController@activatedAutoSweep')->name('admin.loans.auto_sweeping');
   Route::get('/remita-sweeping', 'LoanController@remitaActivatedSweep')->name('admin.loans.remita_sweeping');  
   Route::get('/inactive', 'LoanController@inActiveLoans')->name('admin.loans.inactive'); 
   Route::get('/managed', 'LoanController@managedLoans')->name('admin.loans.managed');
   Route::get('/penalized', 'LoanController@penalizedLoans')->name('admin.loans.penalized');
   Route::get('/restructured', 'LoanController@restructuredLoans')->name('admin.loans.restructured');
   Route::get('/void', 'LoanController@voidLoans')->name('admin.loans.void'); 
   Route::get('/eligible-top-up','LoanController@eligibleTopUp')->name('admin.loans.eligible');

   Route::get('/sweepable','LoanController@sweepableLoans')->name('admin.loans.sweepable');

   Route::get('/sweep/logs/{loan?}','LoanSweeperController@getLogs');
   
   Route::get('/received', 'LoanController@receivedLoans')->name('admin.loans.received'); 
   Route::get('/given', 'LoanController@givenLoans')->name('admin.loans.given'); 
   Route::get('/given/view/{id}', 'LoanController@viewGivenLoan')->name('admin.loans.given.view');
   Route::get('/acquired', 'LoanController@acquiredLoans')->name('admin.loans.acquired'); 
   Route::get('/acquired/view/{id}', 'LoanController@viewAcquiredLoan')->name('admin.loans.acquired.view');
   Route::get('/purchase/available', 'LoanController@getAvailableLoanPurchases')->name('admin.loans.purchase.available');
   Route::get('/fulfilled','LoanController@fulfilledLoans')->name('admin.loans.fulfilled');
   Route::post('/date/fulfilled', 'LoanController@fulfilledLoans')->name('date.fulfilled');
   Route::post('/fulfill/{reference?}', 'LoanController@fulfillLoan')->name('admin.loan.fulfill');
   Route::post('/unfulfill/{reference?}', 'LoanController@unfulfillLoan')->name('admin.loans.unfulfill');
   Route::post('/wallet/zerorise/{reference?}', 'LoanController@zeroriseWallet')->name('admin.loans.zerorise.wallet');
   Route::get('/pending', 'LoanController@pendingLoans')->name('admin.loans.pending');
   Route::get('/pending/retry/{loan?}/{code?}', 'LoanRequestDisbursementController@retryPreparation')->name('admin.loans.pending.retry');
   Route::get('/mandate-status/{loan?}', 'LoanRequestDisbursementController@checkMandateStatus')->name('admin.loans.mandate-status');

   Route::post('/mandate-request-otp/{loan?}', 'MandateController@requestMandateOtp')->name('admin.loans.mandate-request-otp');
   Route::post('/mandate-validate-otp/{loan?}', 'MandateController@validateMandateOtp')->name('admin.loans.mandate-validate-otp');
   Route::get('/mandate-stop/{loan?}','MandateController@stopMandate')->name('admin.loans.mandate-stop');
   Route::get('/mandate-history/{loan?}','MandateController@historyMandate')->name('admin.loans.mandate-history');
   Route::get('/remita/banks','MandateController@getRemitaBanks')->name('admin.loans.remita-banks');
   
   Route::get('/disburse/{loan}', 'LoanRequestDisbursementController@disburseLoan')->name('admin.loans.disburse');
   Route::get('/disburse/backend/{loan?}', 'LoanRequestDisbursementController@disburseLoanBackend')->name('admin.loans.disburse-backend');
   Route::post('/update/sweep-params/{loan?}', 'LoanController@updateSweepParams')->name('admin.loans.sweep-params');
   
   Route::get('/mandates/get-authority-form/{loan?}/{type?}', 'MandateController@getAuthorityForm')->name('admin.loans.mandate-form');
   Route::get('/mandates/approve/{loan?}/{type?}', 'MandateController@approve')->name('admin.loans.mandate-approve');
   Route::get('/mandates/decline/{loan?}/{type?}', 'MandateController@decline')->name('admin.loans.mandate-decline');

   Route::get('/sweep/pause/toggle/{loan?}', 'LoanController@toggleSweepPause')->name('admin.loans.pause-sweep-toggle');
   Route::get('/sweep/auto/toggle/{loan?}', 'LoanController@toggleAutoSweep')->name('admin.loans.auto-sweep-toggle');
   Route::get('/remita/auto/toggle/{loan?}', 'LoanController@toggleAutoRemita')->name('admin.loans.remita-sweep-toggle');
   Route::post('/markAsManaged/{reference?}', 'LoanController@markAsManaged')->name('admin.loans.markasmanaged');
   Route::post('/markAsVoid/{reference?}', 'LoanController@markAsVoid')->name('admin.loans.markasvoid');
   Route::post('/markAsActive/{reference?}', 'LoanController@markAsActive')->name('admin.loans.markasactive');
   Route::post('/restore/void/{reference?}', 'LoanController@restoreVoid')->name('admin.loans.restorevoid');
   Route::post('/markAsInActive/{reference?}', 'LoanController@markAsInActive')->name('admin.loans.markasinactive');
   Route::post('/dissolve/loan/{reference?}', 'LoanController@dissolveLoan')->name('admin.loans.dissolveloan');

   Route::post('/restructure/{loan}', 'LoanRestructureController@restructure')->name('admin.loans.restructure');
   Route::post('/restructure/setup/{loan}', 'LoanRestructureController@setupLoan')->name('admin.loans.setup.loan');

   Route::post('/sweep-loan/{loan}', 'LoanSweeperController@sweepLoan')->name('admin.loans.sweep-single');

   Route::get('/investor/migration', 'LoanMigrationController@index')->name('admin.investor.loanFund.migrate');
   Route::get('/investor/loanFund/currentValue', 'LoanMigrationController@getSelectedFundCurrentValue');
   Route::post('/investor/loanFund/migrate', 'LoanMigrationController@migrate')->name('admin.loanfund.migration');

   Route::get('/group/add', 'BorrowerGroupController@getActiveLoans')->name('admin.group.create');
   Route::post('/group/create', 'BorrowerGroupController@storeGroupedBorrowers')->name('admin.group.store');
   Route::get('/group/view', 'BorrowerGroupController@viewGroups')->name('admin.group.view');
   Route::get('/group/view/{reference?}', 'BorrowerGroupController@viewSingleGroup')->name('admin.group.single-view');

   Route::get('/group/search', 'BorrowerGroupController@search')->name('admin.group.search');
   Route::get('/group/index', 'BorrowerGroupController@index')->name('admin.group.index');

});

Route::group(['prefix'=>'mails'],function(){
   Route::get('/investors','MailController@investor')->name('admin.mails.investors');
   Route::get('/staffs','MailController@staff')->name('admin.mails.staffs');
   Route::get('/affiliates?','MailController@affiliate')->name('admin.mails.affiliates');
   Route::get('/borrowers','MailController@borrower')->name('admin.mails.borrowers');
   Route::post('/post','MailController@sendMail')->name('admin.mails.send');
});

Route::group(['prefix'=>'okra-collections'],function(){
   //Route::get('/view','OkraCollectionController@getOkraBorrowers');
   Route::get('/view','OkraCollectionController@getOkraBorrowers')->name('admin.okra.collection');
   Route::post('/post','OkraCollectionController@updateBalanceID')->name('admin.update.okraID');
   Route::post('/initiatepayment/post','OkraCollectionController@opay')->name('admin.okra.repayment');
   Route::get('/payment-records','OkraCollectionController@okraRecords')->name('admin.okra.records');
   Route::post('/retrieve/balance','OkraCollectionController@retrieveBalance')->name('admin.retrieve.balance');
   Route::post('/verify/payment','OkraCollectionController@verifyOkraPayment')->name('admin.verify.payment');
   Route::post('/records','OkraCollectionController@payInvestors')->name('admin.okra.settlement');
   
});
        
Route::group(['prefix' => 'affiliates', 'middleware' => 'auth:admin'], function() {
   Route::get('/', 'AffiliateController@index')->name('admin.affiliates.index');
   Route::get('/active', 'AffiliateController@getAllActive'); 
   Route::get('/settle', 'AffiliateController@settleAffiliatePage')->name('admin.affiliates.settle'); 
   Route::post('/settle/commission', 'AffiliateController@settleAffiliateCommission')->name('admin.affiliates.settle.commission'); 
   Route::get('/{affiliate?}', 'AffiliateController@show')->name('admin.affiliates.show');
   Route::get('/{affiliate?}/toggle', 'AffiliateController@toggleStatus')->name('admin.affiliates.toggle-status');
   Route::post('/{affiliate?}/verify', 'AffiliateController@verify')->name('admin.affiliates.verify');
   Route::post('/{affiliate?}/update', 'AffiliateController@update')->name('admin.affiliates.update');
   Route::post('/{affiliate?}/supervisor', 'AffiliateController@setSupervisor')->name('admin.affiliates.supervisor');
   Route::post('/{affiliate?}/meeting', 'AffiliateController@scheduleMeeting')->name('admin.affiliates.meeting');
   Route::post('/configure/settings/{affiliate}', 'AffiliateSettingsController@configureSettings')->name('admin.affiliate.settings');

});

Route::group(['prefix'=>'targets', 'middleware'=>'auth:admin'], function() {

   Route::get('/', 'TargetController@index')->name('admin.affiliates.targets');
   Route::get('/data', 'TargetController@getTargets');
   Route::get('/data/{target?}', 'TargetController@getTargetMetrics');
   Route::post('/store', 'TargetController@createTarget')->name('admin.affiliate.target-store');
   Route::put('/update/{target?}', 'TargetController@update')->name('admin.affiliate.target-update');
   Route::delete('/destroy/{target?}', 'TargetController@destroy')->name('admin.affiliate.target-destroy');
});

Route::group(['prefix' => 'withdrawal-requests', 'middleware' => 'auth:admin'], function() {
   Route::get('/declined', 'WithdrawalRequestController@declined')->name('admin.withdrawal-requests.declined'); 
   Route::get('/pending', 'WithdrawalRequestController@pending')->name('admin.withdrawal-requests.pending'); 
   Route::get('/approved', 'WithdrawalRequestController@approved')->name('admin.withdrawal-requests.approved'); 
   Route::get('/approve/{request_id}', 'WithdrawalRequestController@approve')->name('admin.withdrawal-requests.approve'); 
   Route::get('/approve/backend/{request_id}', 'WithdrawalRequestController@approveBackend')->name('admin.withdrawal-requests.approve-backend'); 
   Route::get('/decline/{request_id}', 'WithdrawalRequestController@decline')->name('admin.withdrawal-requests.decline');
   Route::get('/show/{request_id}', 'WithdrawalRequestController@show')->name('admin.withdrawal-requests.show');  
});

Route::group(['prefix' => 'communications', 'middleware' => 'auth:admin'], function() {
   Route::get('/sms', 'Communications\SMSController@getSMS')->name('admin.communications.sms'); 
   Route::post('/sms', 'Communications\SMSController@sendSMS'); 
   Route::get('/conversations', 'Communications\MessageController@conversations')->name('admin.communications.conversations');
   Route::get('/conversations/{entityCode}/{entityId}', 'Communications\MessageController@show')->name('admin.communications.conversations.show');
   Route::post('/conversations/{entityCode}/{entityId}', 'Communications\MessageController@send')->name('admin.communications.conversations.send');
});

Route::group(['prefix' => 'staff'], function() {
   Route::get('/', 'StaffController@index')->name('admin.staff.index');
   Route::get('/invites', 'StaffController@invites')->name('admin.staff.invites');
   Route::post('/invites', 'StaffController@invite');
   Route::get('/invite/delete/{id}', 'StaffController@deleteInvite')->name('admin.staff.invites.delete');
   Route::get('/view/{reference?}', 'StaffController@view')->name('admin.staff.view');
   Route::get('/toggle/{reference?}', 'StaffController@toggle')->name('admin.staff.toggle');
   Route::get('/delete/{reference?}', 'StaffController@delete')->name('admin.staff.delete');
   Route::post('roles/{staff}', 'StaffController@updateRoles')->name('admin.staff.roles');
   Route::post('update/{staff}', 'StaffController@update')->name('admin.staff.update');
});
Route::group(['prefix'=>'settlement'],function(){
   Route::get('/pending','Settlement\SettlementController@pending')->name('admin.settlement.pending');
   Route::get('/confirmed','Settlement\SettlementController@confirmed')->name('admin.settlement.confirmed');
   Route::get('/view/{reference?}','Settlement\SettlementController@view')->name('admin.settlement.view');
   Route::get('/confirm/{reference?}','Settlement\SettlementController@confirm')->name('admin.settlement.confirm');
   Route::post('/decline/{reference?}','Settlement\SettlementController@decline')->name('admin.settlement.decline');
   Route::post('/unconfirm/{reference?}','Settlement\SettlementController@unconfirm')->name('admin.settlement.unconfirm');
   Route::get('/invoice/{reference?}','Settlement\SettlementController@settlementInvoice')->name('settlement.invoice.view');
   Route::get('preview/{reference?}','Settlement\SettlementController@previewDoc')->name('admin.settlement.preview');
   Route::post('payment/loan/{reference}','Settlement\SettlementController@pay')->name('admin.pay.settlement');
    Route::get('payment/callback','Settlement\SettlementController@handleSettlementPaymentCallback')->name('admin.settlement.payment_callback');
});
Route::group(['prefix'=>'loan_transaction'],function(){
   Route::post('/add/{reference}','LoanTransactionController@add')->name('admin.loan.transaction.add');
});
Route::group(['prefix' => 'repayments'],function(){
   Route::get('/payfromwallet/data/{reference?}', 'Repayments\LoanRepaymentController@getPayFromWalletData');
   Route::post('/plan/pay-wallet/{planID}', 'Repayments\LoanRepaymentController@payFromWallet');
   Route::get('/','Repayments\LoanRepaymentController@index')->name('admin.show.repayments');
   Route::post('/confirm/{id}','Repayments\LoanRepaymentController@confirm')->name('admin.repayment.confirm');
   Route::post('/unconfirm/{id}','Repayments\LoanRepaymentController@unconfirm')->name('admin.repayment.unconfirm');

   Route::post('/excel/uploads','Repayments\ImportController@importRepayments')->name('admin.excel.repayments');
   Route::get('/skipped/repayment/download','Repayments\ImportController@download')->name('skipped.repayments.download');

   Route::get('/approve','Repayments\LoanRepaymentController@approve')->name('admin.approve.repayments');
   Route::post('/approve','Repayments\LoanRepaymentController@approveAll')->name('admin.approve.all.repayments');
   Route::post('/delete','Repayments\LoanRepaymentController@deleteAll')->name('admin.delete.all.repayments');
   Route::get('/bulk','Repayments\LoanRepaymentController@bulk')->name('admin.bulk.repayments');
   Route::post('/bulk','Repayments\LoanRepaymentController@bulkRepayment')->name('admin.bulk-repayment');
   Route::get('/bulk/borrowers','Repayments\LoanRepaymentController@getBorrowers');
   Route::delete('/delete/{id}','Repayments\LoanRepaymentController@delete')->name('admin.repayment.delete');
   
});
Route::group(['prefix'=> 'manage-penalty'], function(){
   Route::get('/details/{reference?}', 'PenaltyManagementController@getPenaltyDetails');
   Route::post('/cancel/entry/{id}', 'PenaltyManagementController@cancelEntry');
   Route::post('/save/entry', 'PenaltyManagementController@saveEntry');
   Route::post('/dissolve/{loan?}', 'PenaltyManagementController@dissolvePenalty')->name('admin.dissolve-penalty');
   Route::post('/buildup/{loan?}', 'PenaltyManagementController@buildupPenalty')->name('admin.buildup-penalty');
   Route::post('/dissolve/employer/{employer?}', 'PenaltyManagementController@dissolvePenaltyEmployer')->name('admin.dissolve-penalty-employer');
   Route::post('/buildup/employer/{employer?}', 'PenaltyManagementController@buildupPenaltyEmployer')->name('admin.buildup-penalty-employer');
});
Route::group(['prefix' => 'meetings'], function() {
   Route::get('/', 'MeetingController@index')->name('admin.meetings.index');
   Route::post('/', 'MeetingController@store');
   Route::get('/delete/{meeting}', 'MeetingController@delete')->name('admin.meetings.delete');
   Route::get('/{meeting}', 'MeetingController@show')->name('admin.meetings.show');
   Route::post('/{meeting?}', 'MeetingController@update')->name('admin.meetings.update');
});

Route::group(['prefix'=> 'penalty-settings'], function() {
   Route::post('/create', 'PenaltySettingsController@create');
   Route::post('/update', 'PenaltySettingsController@update');
});

Route::group(['prefix'=>'reports'],function(){
   Route::get('/','ReportController@index')->name('admin.report.view');
   Route::post('/','ReportController@fetchData')->name('admin.report.fetch');
   Route::get('/primary/employers','ReportController@getPrimaryEmployers')->name('admin.report.primary.employers');
   Route::get('/affiliates','ReportController@getAffiliates')->name('admin.report.affiliates');
   Route::get('/investors','ReportController@getInvestors')->name('admin.report.investors');
   Route::get('/{name?}','ReportController@getStatistics')->name('admin.report.statistics');
   Route::post('/send/repayment/notification','ReportController@sendNotification')->name('admin.report.notify');
   Route::post('download/activeLoanDownload', 'ReportController@downloadActiveLoan')->name('admin.download.activeLoan');


});

Route::group(['prefix'=>'loan-wallet-transactions'], function(){
   Route::get('/user/{user}', 'LoanWalletTransactionController@getWalletData');
   Route::post('/store/{loan}', 'LoanWalletTransactionController@store');
   Route::post('/update', 'LoanWalletTransactionController@update');
   Route::post('/delete/{id}', 'LoanWalletTransactionController@delete')->name('admin.delete.wallet-trnx');
});

Route::group(['prefix'=> 'loan-funds'], function(){
   Route::get('/', 'LoanFundController@index')->name('admin.loan-funds.index');
   Route::get('/report', 'LoanFundController@report')->name('admin.loan-funds.report');
});

Route::group(['prefix'=>'refunds'], function(){
   Route::get('/logs', 'RefundController@log')->name('admin.refund.logs');
   Route::get('/pending', 'RefundController@pendingRefund')->name('admin.refund.pending');
   Route::post('/store', 'RefundController@store')->name('admin.create.refund');
   Route::patch('/update/{id?}/{status?}/', 'RefundController@update')->name('admin.update.refund');
});


Route::group(['prefix'=>'paystack'], function() {
   Route::get('/incomplete/profile/customers', 'PaystackCustomerSyncController@incompleteProfileCustomers');
   Route::get('/all/profile/customers', 'PaystackCustomerSyncController@getAllCustomers');

   Route::get('/sync/show', 'PaystackCustomerSyncController@index')->name('admin.paystack.sync.show');

   Route::post('/sync', 'PaystackCustomerSyncController@sync')->name('admin.paystack.sync');
});

Route::post('/investor/assign/commission/{investor?}', 'InvestorController@assignCommissionReceiver')->name('admin.investors.assign-commission');
Route::post('/investor/unassign/commission/{investor?}', 'InvestorController@unAssignCommissionReceiver')->name('admin.investors.unassign-commission');

Route::get('/wallet-fund/commission/data', 'AccountFundController@allNeededDataWalletFund');

Route::get('/promissory-note/commission/data', 'AccountFundController@allNeededDataPromissoryNote');

Route::post('/investor/pay/note/commission', 'AccountFundController@payPNoteCommission')->name('admin.investors.pay-note-commission');
Route::post('/investor/pay/commission', 'AccountFundController@payFundCommission')->name('admin.investors.pay-fund-commission');
Route::post('employments/{employment}', 'EmploymentController@update')->name('admin.employments.update');





// The goal is to move staff admin routes here to avoid duplicate controllers

Route::group(
   ['prefix'=>'staff-roles'], 
    function () {

       Route::group(
         ['middleware'=>'staff.roles:loan_request'], 
            
         function () {

            Route::group(['prefix' => 'loan-requests'], function () {

               
               Route::post('/store', 'LoanRequestController@store')->name('staff.loan-requests.store');
               Route::post('/update', 'LoanRequestController@updateLoanRequest')->name('staff.loan-requests.update');
               Route::post('loan-request/approve', 'LoanRequestController@approveLoanRequest')->name('staff.loan-requests.approve');
               Route::post('prepare/{loanRequest}', 'LoanRequestDisbursementController@prepareLoan')->name('staff.loan-requests.prepare-loan');
               Route::get('loan-request/decline/{reference?}', 'LoanRequestController@declineLoanRequest')->name('staff.loan-requests.decline');
               Route::get('loan-request/refer/{reference?}', 'LoanRequestController@referLoanRequest')->name('staff.loan-requests.refer');
               Route::post('loan-request/assign/{reference?}', 'LoanRequestController@assignLoanRequest')->name('staff.loan-requests.assign');
               Route::post('loan-request/unassign/{reference?}', 'LoanRequestController@unassignLoanRequest')->name('staff.loan-requests.unassign');
           });
         }
       );

       
    }
);

