<?php

/** Broadcast route for affiliates **/
Route::post('/auth/broadcasting', function(Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
})->middleware('auth:affiliate');

Route::get('/login', 'Auth\LoginController@getLogin')->name('affiliates.login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout')->name('affiliates.logout');

Route::get('/register', 'Auth\RegisterController@getRegister')->name('affiliates.register');
Route::post('/register', 'Auth\RegisterController@register');

Route::view('/password-forgot', 'auth.affiliates.passwords.email')->name('affiliates.passwords.forgot');
Route::post('/password-forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('affiliates.passwords.reset');
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('affiliates.passwords.request');

Route::get('/waiting/{condition}', 'DashboardController@waitingArea')->name('affiliates.waiting-area');
Route::post('/verification-apply', 'DashboardController@applyForVerification')->name('affiliates.verification.apply');

Route::group(['middleware' => 'affiliate-status'], function() {
        
    Route::get('/dashboard', 'DashboardController@index')->name('affiliates.dashboard');
    
    Route::get('/withdrawals', 'WithdrawalRequestController@index')->name('affiliates.withdrawals.index');
    Route::post('/withdrawals/store', 'WithdrawalRequestController@store')->name('affiliates.withdrawals.store');
    
    Route::get('/transactions', 'TransactionController@index')->name('affiliates.transactions.wallet');

    Route::get('/commisions/received', 'CommissionsController@index')->name('affiliates.commissions.index');
    
    Route::get('/profile', 'ProfileController@index')->name('affiliates.profile.index');
    Route::post('/profile', 'ProfileController@update');
    Route::post('/profile/password', 'ProfileController@updatePassword')->name('affiliates.profile.password');
    Route::post('/profile/bank', 'ProfileController@bankUpdate')->name('affiliates.profile.bank');
    
    Route::get('/messages', 'MessageController@index')->name('affiliates.messages.index');
    Route::get('/messages/{entityCode}/{entityId}', 'MessageController@show')->name('affiliates.messages.show');
    Route::post('/messages/store/admin', 'MessageController@sendMessageToAdmin')->name('affiliates.messages.send-admin');
    
    Route::get('/accounts/borrowers', 'BorrowerController@index')->name('affiliates.borrowers');
  
    Route::get('/accounts/borrowers/new', 'BorrowerController@create')->name('affiliates.borrowers.create');
    Route::get('/accounts/borrowers/{user}', 'BorrowerController@show')->name('affiliates.borrowers.show');
    Route::post('/accounts/borrowers', 'BorrowerController@store')->name('affiliates.borrowers.store');
    Route::post('/accounts/borrowers/documents', 'BorrowerController@uploadDocuments')->name('affiliates.documents.borrowers');
    
    Route::post('/resubmit/{loanRequest}', 'LoanRequestController@resubmit')->name('affiliates.loan-requests.resubmit');

    Route::get('loan-requests', 'LoanRequestController@index')->name('affiliates.loan-requests');
    Route::get('/loan-requests/getBorrowers', 'LoanRequestController@getBorrowers')->name('affiliates.loan-requests.borrowers');
    
    Route::get('loan-requests/create', 'LoanRequestController@create')->name('affiliates.loan-requests.create');
    Route::get('/loan-requests/users', 'LoanRequestController@getBorrowers')->name('affiliates.loan-requests.users');
    Route::post('loan-requests', 'LoanRequestController@store')->name('affiliates.loan-requests.store');
    Route::get('loan-requests/store/pay', 'LoanRequestController@handleApplicationPaymentResponse')->name('affiliates.loan-requests.store.pay');
    Route::get('/loan-requests/max-amount/{reference?}/{duration?}/{employment?}', 'LoanRequestController@checkMaxRequestAmount')->name('affiliates.loan-requests.checkmax');
    Route::get('/loan-requests/emi-amount/{duration?}/{employment?}/{amount?}', 'LoanRequestController@checkMonthlyRepayment')->name('affiliates.loan-requests.checkemi');
    Route::get('/loan-requests/{loanRequest}', 'LoanRequestController@show')->name('affiliates.loan-requests.show');
    Route::get('/loan-requests/cancel/{loanRequest}', 'LoanRequestController@cancel')->name('affiliates.loan-requests.cancel');
    Route::get('/loan-requests/accept-funds/{loanRequest}', 'LoanRequestController@accept')->name('affiliates.loan-requests.accept-funds');
    
    Route::get('loans/eligible-top-up','LoanController@eligibleTopUp')->name('affiliates.loans.eligible');
    Route::get('loans/active', 'LoanController@activeLoans')->name('affiliates.loans.active');
    Route::get('loans/fulfilled','LoanController@fulfilledLoans')->name('affiliates.loans.fulfilled');
    Route::get('loans/{loan}', 'LoanController@view')->name('affiliates.loans.view');
    Route::get('loans/mandates/authority-form/{loan}/{type?}', 'MandateController@getAuthorityForm')->name('affiliates.loans.authorityForm');
    Route::post('loans/mandates/authority-form/{loan}', 'MandateController@uploadAuthorityForm')->name('affiliates.loans.authorityForm.upload');
    
    Route::post('/employers', 'EmployerController@store')->name('affiliates.employers');
    
    Route::post('employments/update/payroll/{employment}', 'EmploymentController@updatePayRoll')->name('affiliate.employments.update_payroll');
    Route::post('employments/{employment}', 'EmploymentController@update')->name('affiliates.employments.update');
    
    Route::group(['prefix'=>'targets'], function() {
        Route::get('/data', 'TargetController@activeTargets')->name('affiliates.target.data');
    });
});
