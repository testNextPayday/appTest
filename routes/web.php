<?php

use App\Exports\UserExport;
use App\Models\RepaymentPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Unicredit\Collection\CardService;
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: *');
// header('Access-Control-Allow-Credentials: true');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/dev/fix', 'Dev\LoanManager@fix');



// Route::get('/try/card',function(){

//     $users = App\Models\User::has('billingCards')->with('billingCards')->get();
//     // $card = new CardService();
//     // $repayment = RepaymentPlan::find(request('plan'));
//     // dd($repayment);
//     return view('card_users',compact('users'));


// });


Route::get('/loan-recova/{bvn}', 'ApiController@loanRecova');



Route::get('/devtricks/codewars/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');



Route::get('/test', 'Devs\TestController@test');
Route::get('/phone/password/reset', 'Auth\ForgotPasswordController@usePhone')->name('password.request.phone');

Auth::routes();

Route::get('/resolve/account/number', 'PaystackController@resolveAccount')->name('resolve.account');
Route::post('/resolve/card/{reference}', 'Users\ProfileController@resolveCard')->name('resolve.card');

// New users loan-setup route outside authorization but uses the signed middleware in controllers
Route::get('/page/test',function(){
    
    return view('emails.invoice');
});

Route::post('/notifications/read/{id}', 'Users\NotificationController@markAsRead');

Route::get('/paystack/reference','PaystackController@getTransRef');

Route::get('/loan/{loan}','Users\LoanController@getLoan');

Route::post('/loan/okra-setup', 'Users\OkraController@authAccount')->name('auth.accnt');
//Route::get('/loan/mono/verify', 'Users\MonoController@verifyStatus')->name('users.mono.verify.status');

Route::group(['prefix'=>'loan-setup', 'namespace'=>'Users'], function(){

Route::get('/{loan}', 'LoanSetupController@showSetupForm')->name('users.loan.setup');
Route::get('/mono/payment/verify', 'MonoController@paymentSetup');
Route::post('/mono/status', 'MonoController@verifyStatus')->name('users.mono.verify.status');
  
Route::group(['middleware'=>'notification'], 

        function () {

            Route::get('/dashboard/{loan}','LoanSetupController@setupFormDashboard')->name('users.loan.setup-dashboard');
           
            
        }
    );

   

    Route::post('/card-setup/verify/{loan}','LoanSetupController@verifyUserCardSetup')->name('users.loan-setup.card-setup');

});

Route::group(
    ['prefix'=>'notifications', 'namespace'=>'Users'],

    function () {

        Route::get('/', 'NotificationController@index')->name('users.notification.index');

    }
);




Route::post('/register/modified', 'Auth\ModifiedRegisterController@register');

Route::post('/remita/notify/repayment', 'RemitaNotificationsController@loanRepayment');
Route::get('/home', 'HomeController@index')->name('home');

//social routes

Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');


Route::post('/pay', 'PaystackController@redirectToGateway')->name('pay');
Route::get('pay/callback', 'PaystackController@handleGatewayCallback');
Route::post('/paystack/verify/transaction/repaymentplan', 'PaystackController@verifyUserPlansPayment')->name('users.installment.verify-trnx');

Route::post('/verify/bvn', 'Users\ProfileController@verifyBVN');

//supervisor or employer verifications
Route::group(['prefix' => 'verifications'], function () {
    Route::get('email/verification/resend', 'Auth\ModifiedRegisterController@resendEmailVerification')->name('email.verification.resend');
    Route::get('email/unverified', 'Auth\ModifiedRegisterController@unverifiedEmail')->name('email.unverified')->middleware('joint:web,investor');
    Route::get('email/{code}/{email}/{guard}', 'Auth\ModifiedRegisterController@verifyEmail')->name('email.verify');
});

//shared routes
Route::post('payments/wallet-fund/verify', 'PaystackController@verifyWalletFundTransaction');
Route::post('investors/promissory-notes/fund/paystack/payments/verify', 'PaystackController@investorVerifyFundTransaction');



Route::group(['middleware' => ['unverified.email']], function () {
    Route::group(['prefix' => 'profile'], function () {
        // Route::get('/update/lender/activation', 'Users\LenderActivationController@lender')->name('users.profile.lender.activation'); 
        // Route::post('/update/lender/activation', 'Users\LenderActivationController@lenderActivation')->name('users.profile.lender.activation.update');        
    });

});

Route::get('/view/loan/statement/{reference}','LoanStatementController@view_loan_statement')->name('view.loan.statement');
Route::get('/download/loan/statement/{reference}','LoanStatementController@download_loan_statement')->name('download.loan.statement');
Route::get('/mail/loan/statement/{reference}','LoanStatementController@mail_loan_statement')->name('mail.loan.statement');

Route::post('/employer/verify-payment', 'PaystackController@verifyEmployerVerificationPayment')->middleware('auth');

Route::group(['middleware' => ['auth', 'unverified.email'], 'namespace' => 'Users'], function () {

    Route::get('/', 'UserController@dashboard')->name('users.dashboard');
    Route::get('/settlement/application/loan/{reference}','SettlementController@settlement')->name('users.apply.settlement');
    Route::post('/settlement/payment/loan/{reference}','SettlementController@pay')->name('users.pay.settlement');
    Route::post('/save/settlement/loan/{reference}','SettlementController@save')->name('users.save.settlement');
    Route::get('/settlement/payment/callback','SettlementController@handleSettlementPaymentCallback')->name('users.settlement.payment_callback');

    // Route::group(['prefix' => 'wallet'], function() {
    //     Route::get('/fund', 'Users\LoanRequestController@index')->name('users.loan-requests.index');
    // });

    Route::get('/users/tickets', 'TicketController@index')->name('users.ticket.index');
    Route::get('/users/create/ticket', 'TicketController@create')->name('users.ticket.create');
    Route::get('/users/show/ticket/{ticket}', 'TicketController@show')->name('users.ticket.show');
    Route::post('/users/store/ticket', 'TicketController@store')->name('users.ticket.store');
    Route::post('/users/respond/ticket/{ticket}', 'TicketController@respond')->name('users.ticket.respond');
    Route::post('/users/close/ticket/{ticket}', 'TicketController@close')->name('users.ticket.close');

       
    Route::group(['prefix' => 'billing'], function () {
        Route::get('/', 'BillingController@index')->name('users.billing.index');
        Route::get('/add-card', 'BillingController@handleAddCard')->name('users.billing.add-card');
        Route::post('/add-card', 'BillingController@addCard');
        Route::get('/remove-card/{billingCard}', 'BillingController@removeCard')->name('users.billing.remove-card');
    });


    Route::group(['prefix'=>'refund'], function(){

        Route::get('/index', 'RefundController@index')->name('users.refund.index');
        Route::post('/request', 'RefundController@store')->name('users.refund.request');
    }
    );


    Route::group(['prefix'=>'savings'], function(){

        Route::view('/index', 'users.savings.index')->name('users.savings.index');
        // Route::post('/request', 'RefundController@store')->name('users.refund.request');
    }
    );

    Route::group(['prefix'=>'plans'], function(){

        Route::post('/update/payment', 'RepaymentController@updatePlans')->name('users.update-repaymentplans');
    });

    Route::group(['prefix' => 'loan-requests'], function () {
        Route::get('/', 'LoanRequestController@index')->name('users.loan-requests.index');
        Route::get('/new', 'LoanRequestController@create')->name('users.loan-requests.create');
        Route::get('/view/{loanRequest}', 'LoanRequestController@view')->name('users.loan-requests.view');
        // Route::get('/store/pay', 'LoanRequestController@handleApplicationPaymentResponse')->name('users.loan-requests.store.pay');
        Route::post('/store', 'LoanRequestController@store')->name('users.loan-requests.store');
        Route::post('/resubmit/{loanRequest}', 'LoanRequestController@resubmit')->name('users.loan-requests.resubmit');
        //ENDPOINT FOR PAYMENT VERIFICATION
        Route::post('verify/payment', 'LoanRequestController@verifyPayment')->name('users.loan-requests.verifypayment');
        
        Route::post('/update', 'LoanRequestController@update')->name('users.loan-requests.update');
        Route::get('/cancel/{loanRequest}', 'LoanRequestController@cancel')->name('users.loan-requests.cancel');
        Route::get('/delete/{loanRequest}', 'LoanRequestController@delete')->name('users.loan-requests.delete');
        Route::get('/accept-funds/{loanRequest}', 'LoanRequestController@accept')->name('users.loan-requests.accept-funds');

        Route::post('/assign-investor/{loanRequest}', 'LoanRequestController@assignToInvestor')->name('users.loan-requests.assign-investor');
        Route::group(['prefix' => 'funds'], function () {
        Route::get('/reject/{reference}', 'LoanFundController@rejectLoan')->name('users.loan-requests.funds.reject');
        });

        Route::group(['prefix' => 'remita'], function () {
        Route::get('/mandate/authorize/{mandateId}/{requestId}', 'LoanFundController@requestAuthorization')->name('users.loan-request.mandate.authorize');
        Route::get('/mandate/check/{loanRequest}', 'LoanFundController@verifyMandateStatus')->name('users.loan-request.mandate.check');
        Route::post('/mandate/activate', 'LoanFundController@activateMandate')->name('users.loan-request.mandate.activate');
        });
    });

    Route::group(['prefix' => 'loans'], function () {

        Route::get('/', 'LoanController@receivedLoans')->name('users.loans.received');
        Route::get('/{reference}', 'LoanController@view')->name('users.loans.view');


        Route::get('/mandates/authority-form/{loan}/{type?}', 'MandateController@getAuthorityForm')->name('users.loans.authorityForm');
        Route::post('/mandates/authority-form/{loan}', 'MandateController@uploadAuthorityForm')->name('users.loans.authorityForm.upload');

        Route::get('/mandates/activation/{loan}', 'MandateActivationController@requestAuthorization');
        Route::post('/mandates/activation/{loan}', 'MandateActivationController@activate');
    });

    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/wallet', 'TransactionController@index')->name('users.transactions.wallet');
    });

    Route::group(['prefix' => 'withdrawals'], function () {
        Route::get('/', 'WithdrawalRequestController@index')->name('users.withdrawals.index');
        Route::post('/store', 'WithdrawalRequestController@store')->name('users.withdrawals.request');
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', 'ProfileController@index')->name('users.profile.index');
        Route::get('/password/reset', 'ProfileController@account')->name('users.account.security');
        Route::post('/update/basic', 'ProfileController@basicUpdate')->name('users.profile.basic.update');
        Route::post('/update/personal', 'ProfileController@personalUpdate')->name('users.profile.personal.update');
        Route::post('/update/family', 'ProfileController@familyUpdate')->name('users.profile.family.update');
        Route::post('update/password', 'ProfileController@updatePassword')->name('users.profile.password');
        Route::post('/update/bio', 'ProfileController@bioUpdate')->name('users.profile.bio.update');
        Route::post('/phone-verification/code', 'ProfileController@getVerificationCode');
        Route::post('/phone-verification/verify', 'ProfileController@verifyPhoneNumber');

        


        Route::group(['prefix' => 'employment'], function () {
            Route::post('/add', 'EmploymentController@add');
            Route::post('/update', 'EmploymentController@update');
            Route::post('/upload-documents', 'EmploymentController@uploadDocuments');
            Route::get('/delete/{employment_id}', 'EmploymentController@delete');
        });

        Route::group(['prefix' => 'employer'], function () {
            Route::post('/add', 'EmployerController@store')->name('users.employer.add');

            Route::post('/verify-payment-wallet', 'EmployerController@employerVerificationFromWallet');
        });

        Route::group(['prefix' => 'bank'], function () {
            Route::post('/add', 'BankDetailController@add');
            Route::get('/delete/{bank_id}', 'BankDetailController@delete');
        });
    });
});

Route::group(['prefix'=> 'bank-statement'], function() {

    Route::post('/request', 'Users\BankStatementController@requestStatement');

    Route::post('/checkRequest/{requestID}', 'Users\BankStatementController@checkStatementByID');

    Route::post('/checkRequest/ticket/{ticketNumber}', 'Users\BankStatementController@checkStatementByTicketNo');

    Route::post('/confirm', 'Users\BankStatementController@confirmStatement');

    Route::post('/reconfirm/{requestID}', 'Users\BankStatementController@reConfirmStatement');

    Route::get('/user-details', 'Users\BankStatementController@userDetails');

    Route::get('/retrieve-statement/{ticketNumber}', 'Users\BankStatementController@retrieveStatement');

    Route::get('/check/retrieval/method/{user}', 'Users\BankStatementController@checkRetrievalMethod');
});


Route::group(['prefix' => 'ucnull'], function () {
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::post('logout/', 'Auth\AdminLoginController@logout')->name('admin.logout');

    Route::group(['middleware' => 'auth:admin', 'namespace' => 'Admin'], function () {
        Route::get('bids', 'BidController@index')->name('admin.bids.index');

        Route::get('/dashboard', 'AdminController@dashboard')->name('admin.dashboard');

        // Route::get('/download-data', function(){
        //     // return 'yes';
        //     return (new UserExport)->download('new-user.xlsx');
        // });

        Route::post('download-data', 'DownloadDataController@dbDownload')->name('admin.downloadData');

        // Route::post('/dropbox', 'DropboxController@connect')->name('admin.dropbox.connect');
        // Route::group(['middleware' => ['web', 'DropboxAuthenticated']], function(){
        // // Route::get('dropbox', function(){
        // //         return Dropbox::get('users/get_current_account');
        // //     });
        // // });

        // // Route::get('dropbox/oauth', function(){
        // //     return Dropbox::connect();
        // // });


        Route::group(['prefix' => 'logs'], function () {
            Route::get('/', 'LogController@index')->name('admin.logs.index');
        });

        Route::group(['prefix' => 'sweep'], function () {
            Route::get('employer/{employer}', 'SweepController@sweepEmployerLoans')->name('admin.sweep.employer');
            Route::get('bucket/{bucket}', 'SweepController@sweepBucket')->name('admin.sweep.bucket');
            Route::get('borrower/{user}', 'SweepController@sweepBorrowerAccount')->name('admin.sweep.borrower');
        });

        Route::group(['prefix' => 'loan-requests'], function () {
            Route::get('/', 'LoanRequestController@index')->name('admin.loan-requests.index');
            Route::get('/pending', 'LoanRequestController@pending')->name('admin.loan-requests.pending');
            Route::get('/employer-declined', 'LoanRequestController@employerDeclined')->name('admin.loan-requests.employer-declined');
            Route::get('/pending-setup', 'LoanRequestController@pendingSetup')->name('admin.loan-requests.pending-setup');
            Route::get('/create', 'LoanRequestController@create')->name('admin.loan-requests.create');
            Route::post('/store', 'LoanRequestController@store')->name('admin.loan-requests.store');
            Route::post('/update', 'LoanRequestController@updateLoanRequest')->name('admin.loan-requests.update');
            Route::get('/available', 'LoanRequestController@available')->name('admin.loan-requests.available');
            Route::get('/view/{reference?}', 'LoanRequestController@view')->name('admin.loan-requests.view');
            Route::get('/view/{reference?}/salary', 'LoanRequestController@viewSalaryData')->name('admin.loan-requests.salary');
            Route::post('loan-request/approve', 'LoanRequestController@approveLoanRequest')->name('admin.loan-requests.approve');
            Route::get('loan-request/decline/{reference?}', 'LoanRequestController@declineLoanRequest')->name('admin.loan-requests.decline');
            Route::get('loan-request/refer/{reference?}', 'LoanRequestController@referLoanRequest')->name('admin.loan-requests.refer');
            Route::get('/max-amount/{reference?}/{duration?}', 'LoanRequestController@checkMaxRequestAmount')->name('admin.loan-requests.checkmax');
            Route::get('/emi-amount/{duration?}/{employment?}/{amount?}', 'LoanRequestController@checkMonthlyRepayment')->name('admin.loan-requests.checkemi');


            Route::post('prepare/{loanRequest}', 'LoanRequestDisbursementController@prepareLoan')->name('admin.loan-requests.prepare-loan');

            Route::post('loan-request/assign/{reference}', 'LoanRequestController@assignLoanRequest')->name('admin.loan-requests.assign');
               Route::post('loan-request/unassign/{reference}', 'LoanRequestController@unassignLoanRequest')->name('admin.loan-requests.unassign');
        });

        Route::group(['prefix' => 'certificates'], function () {
            Route::get('/investments', 'CertificateController@investmentCertificates')->name('admin.certificates.investments.index');
            Route::get('/investments/archived', 'CertificateController@archivedCertificates')->name('admin.certificates.investments.archived');
            Route::get('/investments/new', 'CertificateController@createInvestmentCertificate')->name('admin.certificates.investments.new');
            Route::post('/investments/new', 'CertificateController@storeInvestmentCertificate')->name('admin.certificates.investment.store');
            Route::post('/investments/delete/{id}','CertificateController@deleteInvestmentCertificate')->name('admin.certificates.investments.delete');
        });

        Route::group(['prefix' => 'hilcop-certificates'], function () {
            Route::get('/investments', 'HilcopCertificateController@HilcopCertifications')->name('admin.hilcop-certificates.investments.index');
            Route::get('/investments/new', 'HilcopCertificateController@createHilcopCertification')->name('admin.hilcop-certificates.investments.new');
            Route::post('/investments/new', 'HilcopCertificateController@storeHilcopCertification');
            Route::post('/investments/delete/{id}','HilcopCertificateController@deleteHilcopCertification')->name('admin.hilcop-certificates.investments.delete');
        });


        Route::group(['prefix' => 'repayments'], function () {
            Route::get('/create', 'Repayments\LoanRepaymentController@index')->name('admin.repayments.new');
            Route::get('/due-today', 'LoanRepaymentController@dueToday')->name('admin.repayments.due_today');
            Route::get('/insight', 'LoanRepaymentController@insight')->name('admin.repayments.insight');
            Route::get('/due-current-month', 'LoanRepaymentController@dueCurrentMonth')->name('admin.repayments.due_current_month');
            Route::get('/overdue', 'LoanRepaymentController@overdue')->name('admin.repayments.overdue');
            Route::get('/unpaid', 'LoanRepaymentController@unpaid')->name('admin.repayments.unpaid');
            Route::get('/paid', 'LoanRepaymentController@paid')->name('admin.repayments.paid');
            Route::get('/issue-debit/{plan_id}', 'LoanRepaymentController@issueDirectDebitOrder')->name('admin.repayments.order_debit');
            Route::get('stop/{loan}', 'LoanRepaymentController@stopCollection')->name('admin.repayments.stop');
            Route::get('logs', 'LogController@getRepaymentLogs')->name('admin.repayments.logs');

            Route::post('try-card/{repaymentPlan}', 'CollectionController@tryCard')->name('admin.repayments.try-card');
            Route::post('use-ddm/{repaymentPlan}', 'CollectionController@useDDM')->name('admin.repayments.use-ddm');
            Route::get('/manage', 'Repayments\ManagementController@index')->name('admin.repayments.manage');
            Route::get('/export', 'Repayments\ExportController@exportDue')->name('admin.repayments.export-due');
            Route::post('/import', 'Repayments\ImportController@importRepayments')->name('admin.repayments.import');
        });

        Route::group(['prefix' => 'employers'], function () {

            Route::get('/manage/{employer?}', 'EmployerController@manage')->name('admin.employers.manage');
            Route::get('/view/{employer}', 'EmployerController@view')->name('admin.employers.view');
            Route::get('/disable/{employer_id}', 'EmployerController@disableEmployer')->name('admin.employers.disable');
            Route::get('employees/{employer}', 'EmployerController@getEmployees')->name('admin.employers.employees');
            Route::get('employees/loans/{employer}', 'EmployerController@getEmployeeLoans')->name('admin.employers.employees.loans');
            Route::post('/', 'EmployerController@store')->name('admin.employers.store');
            Route::post('/verified', 'EmployerController@verifyEmployer')->name('admin.employers.markVerified');
            Route::post('/status', 'EmployerController@setEmployerStatus')->name('admin.employers.setStatus');
            Route::post('/update/sweep-params/{employer}', 'EmployerController@updateSweepParams')->name('admin.employers.sweep-params');

            Route::post('/{employer}', 'EmployerController@update')->name('admin.employers.update');
            Route::get('/{status?}', 'EmployerController@index')->name('admin.employers.index');

            Route::post('/documents/required/{employer}', 'EmployerController@documentsRequired');
            Route::post('/loanLimit/{employer}', 'EmployerController@loanLimit')->name('admin.loan.limit');
            Route::post('/loanRequest/docs/settings/{employer}', 'EmployerController@loanDocs')->name('admin.loanDocs.settings');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'UserController@index')->name('admin.users.index');
            Route::get('/salary', 'UserController@getSalaryInfoView')->name('admin.users.salary');
            Route::get('/wallet/bal', 'UserController@getWalletBal')->name('admin.users.wallet.bal');
            
            Route::post('/salary', 'UserController@getSalaryInfo');
            Route::get('/view/{user?}', 'UserController@view')->name('admin.users.view');
            Route::post('/topup-wallet', 'UserController@topUpWallet')->name('admin.users.topup_wallet');

            Route::post('/update-user-info', 'UserController@updateUserInfo')->name('admin.users.updateInfo');

            Route::get('/das-eligibility/{user?}', 'UserController@viewSalaryInfo')->name('admin.users.das');
            Route::get('/toggle/{user_id}', 'UserController@toggle')->name('admin.users.toggle');
            Route::get('/upgrade/{reference}', 'UserController@upgrade')->name('admin.users.upgrade');
            Route::post('/loanpermission', 'UserController@enableSalaryNow')->name('admin.enable.salaryloan');
            Route::post('/store', 'UserController@store')->name('admin.users.store');
            Route::post('/assign-staff', 'UserController@assignStaff')->name('admin.users.assign-staff');
            Route::post('/fund-wallet', 'AccountFundController@fundWallet')->name('admin.users.fund-wallet'); 
            Route::post('/upgrade', 'UserController@upgradeSalaryPercentage')->name('admin.users.upgrade');           
        });

        Route::group(['prefix' => 'investors'], function () {
            Route::get('/', 'InvestorController@index')->name('admin.investors.index');
            Route::get('/active', 'InvestorController@activeInvestors')->name('admin.investors.active');
            Route::get('/inactive', 'InvestorController@inactiveInvestors')->name('admin.investors.inactive');
            Route::get('/individuals', 'InvestorController@individuals')->name('admin.investors.individuals');
            Route::get('/corporate', 'InvestorController@corporate')->name('admin.investors.corporate');
            Route::get('/view/{investor}', 'InvestorController@view')->name('admin.investors.view');
            Route::get('/toggle/{investor}', 'InvestorController@toggle')->name('admin.investors.toggle');
            Route::post('/update/{investor}', 'InvestorController@update')->name('admin.investors.update');
            Route::get('/unverified', 'InvestorController@unverified')->name('admin.investors.unverified');

            Route::get('/create', 'InvestorController@create')->name('admin.investors.create');
            Route::post('/create', 'InvestorController@store');
            Route::get('/apply/{reference?}', 'InvestorController@showApplication')->name('admin.investors.apply');
            Route::post('/apply', 'InvestorController@apply')->name('admin.investors.submit-application');
        });

        Route::get('accounts/{model}/{type}', 'AccountsController@index')->name('admin.accounts');

        Route::group(['prefix' => 'transactions'], function () {
            Route::get('/', 'TransactionController@index')->name('admin.transactions.index');
        });

        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', 'SettingsController@index')->name('admin.settings.index');
            Route::post('/update', 'SettingsController@update')->name('admin.settings.update');
        });

        Route::group(['prefix' => 'investor-verifications'], function () {
            Route::get('pending', 'InvestorVerificationController@pending')->name('admin.investor-verifications.pending');
            Route::get('approved', 'InvestorVerificationController@approved')->name('admin.investor-verifications.approved');
            Route::get('declined', 'InvestorVerificationController@declined')->name('admin.investor-verifications.declined');
            Route::get('decline/{investorVerificationRequest}', 'InvestorVerificationController@decline')->name('admin.investor-verifications.decline');
            Route::post('approve', 'InvestorVerificationController@approve')->name('admin.investor-verifications.approve');
        });

        Route::group(['prefix' => 'buckets'], function () {
            Route::get('/', 'BucketController@index')->name('admin.buckets.index');
            Route::get('/{bucket}', 'BucketController@show')->name('admin.buckets.show');
            Route::get('/{bucket}/delete', 'BucketController@delete')->name('admin.buckets.delete');
            Route::post('/', 'BucketController@store')->name('admin.buckets.store');
            Route::post('/{bucket}', 'BucketController@update')->name('admin.buckets.update');
            Route::get('/{employer}/remove', 'BucketEmployerController@removeEmployerFromBucket')->name('admin.buckets.employer.remove');
            Route::post('/{bucket}/employers', 'BucketEmployerController@addEmployersToBucket')->name('admin.buckets.employers.add');
        });
    });
});


/** Staff Leftover Routes **/
Route::group(['prefix' => 'staff'], function () { });


/** New Investors Routes **/
Route::group(['namespace' => 'Investors'], function () {
    Route::get('investor/index', 'PaydayNoteSignupController@index')->name('investors.landingpage');
    Route::get('investor/signup', 'PaydayNoteSignupController@verifyContact')->name('investors.signup');   
    Route::get('investor/signin', 'PaydayNoteSignupController@loginPage')->name('investors.signin'); 
    Route::post('investor/verify-signin', 'PaydayNoteSignupController@login')->name('investors.verify.signin'); 
    Route::post('investor/sendotp', 'PaydayNoteSignupController@sendToken')->name('investors.send.otp');
    Route::get('investor/verify-otp', 'PaydayNoteSignupController@verifyOTP')->name('investors.enter.otp');
    Route::post('investor/verifyotp', 'PaydayNoteSignupController@verifyToken')->name('investors.verify.otp');
    Route::get('investor/basic-profile', 'PaydayNoteSignupController@basicProfile')->name('investors.enter.profile.data');
    Route::post('investor/basic-profile/save', 'PaydayNoteSignupController@store2')->name('investors.save.profile.data'); 
    Route::get('investor/bank-details', 'PaydayNoteSignupController@bankDetails')->name('investors.enter.bank.details');       
    Route::post('investor/bankdetails/create', 'PaydayNoteSignupController@store')->name('investors.save.bank.data');
    
    //Route::post('investor/register', 'AuthController@login')->name('investors.login');

    Route::view('investor/login', 'auth.investors.login')->name('investors.login');
    Route::post('investor/login', 'AuthController@login')->name('investors.login');
    Route::view('investor/password-forgot', 'auth.investors.passwords.email')->name('investors.passwords.forgot');
    Route::post('investor/password-forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('investor/password/reset', 'Auth\ResetPasswordController@reset')->name('investors.passwords.reset');
    Route::get('investor/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('investors.passwords.request');

    // Route::post('investor/register', 'Auth\StaffRegisterController@createStaff')->name('staff.register');

    Route::get('investor/verification', 'VerificationController@index')->name('investors.verification');
    Route::post('investor/verification', 'VerificationController@apply');

    Route::group(['middleware' => 'unverified.investor'], function () {

       

        Route::get('investor/logout', 'AuthController@logout')->name('investors.logout');


        Route::get('requests/marketplace', 'LoanRequestController@index')->name('investors.loan-requests.active');
        Route::get('requests/assigned', 'LoanRequestController@assigned')->name('investors.loan-requests.assigned');
        Route::post('requests/{loanRequest}/fund', 'LoanFundController@store')->name('investors.loan.fund');

        Route::get('funds', 'LoanFundController@index')->name('investors.funds');
        Route::get('funds/acquired', 'LoanFundController@acquired')->name('investors.funds.acquired');
        Route::get('funds/market', 'LoanFundController@market')->name('investors.funds.market');
        Route::get('funds/{loanFund}', 'LoanFundController@show')->name('investors.funds.show');
        Route::post('funds/{loanFund}/transfer', 'LoanFundController@transfer')->name('investors.funds.transfer');

        Route::post('funds/{loanFund}/market/bid', 'BidController@store');

        Route::get('bids', 'BidController@index')->name('investors.bids.index');
        Route::post('bids/{bid}/accept', 'BidController@accept');
        Route::post('bids/{bid}/reject', 'BidController@reject');
        Route::post('bids/{bid}/cancel', 'BidController@cancel');

        Route::get('investor', 'DashboardController@index')->name('investors.dashboard');

        Route::get('investor/profile', 'ProfileController@index')->name('investors.profile');
        Route::post('investor/profile', 'ProfileController@update');
        Route::post('investor/profile/password', 'ProfileController@updatePassword')->name('investors.profile.password');
        Route::post('investor/profile/dashboard', 'ProfileController@updateDashboardType')->name('investors.profile.dashboard');
        Route::post('investor/profile/bank', 'ProfileController@bankUpdate')->name('investors.profile.bank');

        Route::get('/investor/investment-profile','ProfileController@setupInvestmentProfile')->name('investors.investment-profile');
        
        Route::post('investor/transfer/funds/wallet-vault/{sender_id}','WalletTransferController@initiateTransfer')->name('investor.transfer.wallet-vault');
        Route::post('investor/setup-investment-profile','ProfileController@saveInvestmentProfile')->name('investor.setup.investment.profile');

        Route::get('investor/withdrawals', 'WithdrawalController@index')->name('investors.withdrawals');
        Route::post('investor/withdrawals', 'WithdrawalController@store')->name('investors.post.withdrawals');

        Route::get('investor/transactions', 'TransactionController@index')->name('investors.transactions');
        Route::get('investor/transactions/inflow', 'TransactionController@showInflow')->name('investors.transactions.inflow');
        Route::get('investor/collections', 'CollectionController@index')->name('investors.collections');
        Route::post('investor/transaction/email', 'TransactionController@emailTransaction')->name('investors.transactions.email');

        Route::get('investor/certificate','CertificateController@index')->name('investors.certificate.show');
        Route::get('investor/certificate/pdf/{reference}','CertificateController@pdf')->name('investors.certificate.pdf');

        Route::get('/tickets', 'TicketController@index')->name('investors.ticket.index');
        Route::get('/show/ticket/{ticket}', 'TicketController@show')->name('investors.ticket.show');
        Route::post('/store/ticket', 'TicketController@store')->name('investors.ticket.store');
        Route::post('/respond/ticket/{ticket}', 'TicketController@respond')->name('investors.ticket.respond');
        Route::post('/close/ticket/{ticket}', 'TicketController@close')->name('investors.ticket.close');

        Route::group(['prefix'=> 'investors/promissory-notes'], function(){
            
            Route::get('/index', 'PromissoryController@index')->name('investors.promissory-note.index');
            Route::get('/active', 'PromissoryController@active')->name('investors.promissory-note.active');
            Route::get('/view/{promissory_note}', 'PromissoryController@view')->name('investors.promissory-note.view');

            Route::post('/liquidate/{promissory_note}', 'PromissoryController@liquidate')->name('investors.promissory-note.liquidate');
            Route::post('/rollover/{promissory_note}', 'PromissoryController@rollover')->name('investors.promissory-note.rollover');
            Route::post('/withdraw/{promissory_note}', 'PromissoryController@withdraw')->name('investors.promissory-note.withdraw');

            Route::get('/fund/wallet', 'PromissoryController@fundAccount')->name('investors.promissory-note.fund.account');
            Route::post('/payday/mono-fund', 'PromissoryController@monoDirectPay')->name('investors.promissory-note.mono.directpay');
        });
      
    });
});

// Token Login
Route::post('/oauth/magic', 'Auth\SignInController@authUser')->name('magic.login');

/** New Employer Routes **/
Route::group(['prefix' => 'employers', 'namespace' => 'Employers'], function () {

    Route::group(['prefix' => 'loan-requests'], function () {
        Route::get('approve/{code}/{reference}', 'LoanRequestController@approve')->name('employers.loan-requests.approve');
        Route::get('decline/{code}/{reference}', 'LoanRequestController@getDeclineForm')->name('employers.loan-requests.decline-form');
        Route::post('decline', 'LoanRequestController@decline')->name('employers.loan-requests.decline');
    });
});


Route::group(['prefix'=> 'loan-notes'], function() {
    Route::post('/store', 'LoanNoteController@store')->name('app.loan-notes.store');

    Route::post('/update/{note}', 'LoanNoteController@update')->name('app.loan-notes.update');

    Route::post('/delete/{note}', 'LoanNoteController@delete')->name('app.loan-notes.delete');
});

//MONO API REQUEST
Route::group(['prefix'=> 'users/mono'], function() {
    Route::get('/create', 'Users\MonoController@submitRequest');
    Route::post('/authentication', 'Users\MonoController@authHttpRequest')->name('mono.auth');
    Route::post('/getaccount', 'Users\MonoController@getAccount')->name('mono.getaccount');
    Route::get('/statement', 'Users\MonoController@getStatement')->name('mono.statement');
    Route::get('/checkMonoStatus', 'Users\MonoController@checkMonoStatus')->name('mono.check.status');
    Route::post('/verifyIfBankMatch', 'Users\MonoController@verifyBank')->name('mono.verify.bank');
});

//LYDIA API REQUEST
Route::group(['prefix'=> 'users/lydia'], function() {
    Route::post('/create', 'Users\LydiaCollectionController@create')->name('lydia.create');
    // Route::post('/authentication', 'Users\MonoController@authHttpRequest')->name('mono.auth');
    // Route::post('/getaccount', 'Users\MonoController@getAccount')->name('mono.getaccount');
    // Route::get('/statement', 'Users\MonoController@getStatement')->name('mono.statement');
    // Route::get('/checkMonoStatus', 'Users\MonoController@checkMonoStatus')->name('mono.check.status');
    // Route::post('/verifyIfBankMatch', 'Users\MonoController@verifyBank')->name('mono.verify.bank');
});

Route::post('/investors/promissory-notes/fund/payday/mono-fund', 'Investors\PaydayNoteSignupController@monoDirectPay')->name('promissory-note.mono.directpay');
Route::get('/investors/promissory-notes/monostatus', 'Investors\PaydayNoteSignupController@monoStatus')->name('promissory-note.monostatus');
Route::get('/investors/promissory-notes/verify-monostatus', 'Investors\PaydayNoteSignupController@verifyMonoStatus')->name('promissory-note.verifystatus');
// General Route
Route::get('/loan/fulfilment/doc/{reference}', 'LoanFulfillmentDocController@previewDoc')->name('view.loan.fulfillment-doc');


//OKRA API REQUEST
Route::group(['prefix'=> 'users/okra'], function() {    
    Route::post('/webhook', 'Users\OkraController@okraWebhook');    
    //Route::get('/payment', 'OkraController@submitRequest');
});

Route::get('users/okra/payment', 'Users\BankStatementController@viewBankId');
Route::post('bnk/id', 'Users\BankStatementController@getbankId')->name('mbs.bank.statement');



Route::get('/loan-recova/{bvn}', 'ApiController@loanRecova');

Route::group(['prefix' => 'recova-loans'], function () {


    Route::get('/loan-reference/{reference}', 'LoanController@viewLoan');


    Route::get('/mandates/authority-form/{loan}/{type?}', 'MandateController@getAuthorityForm')->name('users.loans.authorityForm');
    Route::post('/mandates/authority-form/{loan}', 'MandateController@uploadAuthorityForm')->name('users.loans.authorityForm.upload');

    Route::get('/mandates/activation/{loan}', 'MandateActivationController@requestAuthorization');
    Route::post('/mandates/activation/{loan}', 'MandateActivationController@activate');
});






Route::group(['prefix' => 'paystack'], function () {
    Route::post('/create-virtual-account', 'VirtualAccountController@create')->name('create_virtual_account');
    Route::post('/webhook-payment-processing', 'VirtualAccountController@webhook');
});
