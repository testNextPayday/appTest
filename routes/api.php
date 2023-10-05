<?php

use App\Models\Investor;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/investor/repaymentplans', function () {
    $investor = Investor::find(3);
    return $investor->repaymentPlanCollection();
});
Route::get('get/investor/statistic/{id}', 'ApiController@getInvestor');

Route::get('/repayments/borrowers', 'SearchBulkRepaymentController@searchBorrowers');

Route::group(['prefix'=>'admin','namespace'=>'Admin'], function () {
    Route::get('/search/borrower/loans', 'SearchController@search')->name('admin.search.borrower.loan');
    Route::get('/all/borrower/loans', 'SearchController@getAllBorrower')->name('admin.all.borrower.loan');
});

Route::group(['prefix'=>'/v1/npd'], function () {
    Route::post('/register', 'Auth\SignUpController@register')->name('api.register');
    Route::post('/register/salary-now', 'Auth\SignUpController@register')->name('api.register.salary-now');
    Route::post('/check/email', 'Auth\SignUpController@checkEmail')->name('api.checkEmail');
    Route::post('/send/token', 'Auth\SignUpController@sendToken')->name('api.sendToken');
    Route::post('/verify/token', 'Auth\SignUpController@verifyToken')->name('api.verify.token');
    Route::get('/employers', 'Auth\SignUpController@getEmployers')->name('api.fetch.employers');

    Route::post('/password/reset/code', 'Auth\ApiPasswordResetController@resetPhone')->name('api.reset-password.code');
    Route::post('/confirm/reset/code', 'Auth\ApiPasswordResetController@confirmPhone')->name('api.reset-password.confirm');
    Route::post('/password/create', 'Auth\ApiPasswordResetController@createPassword')->name('api.create.password');

    Route::post('/login', 'Auth\SignInController@login')->name('api.login');
    
    // investors group
    Route::group(['prefix' => 'investors', 'namespace' => 'Investors'], function () {
        Route::get('/banks', 'AuthApiController@banks');
        Route::post('/login', 'AuthApiController@login')->name('api.investor.login');
        Route::post('/register', 'AuthApiController@store')->name('api.investor.register');

        Route::post(
            '/password/reset/code', 'Auth\ApiPasswordResetController@resetPhone'
        )->name('api.investor.reset-password.code');

        Route::post(
            '/confirm/reset/code', 'Auth\ApiPasswordResetController@confirmPhone'
        )->name('api.investor.reset-password.confirm');

        Route::post(
            '/password/create', 'Auth\ApiPasswordResetController@createPassword'
        )->name('api.investor.create.password');

        Route::group(['middleware'=>'auth:api'], function () {
            Route::post('/logout', 'AuthApiController@logout')->name('api.investor.logout');
        });
    });

    Route::group(['middleware'=>'auth:api'], function () {
        Route::post('/logout', 'Auth\SignInController@logout')->name('api.logout');
    });

    Route::any('{url?}/{sub_url?}', function () {
        return response()->json([
            'status'    => false,
            'message'   => 'Invalid URL.',
        ], 404);
    })->name('api.404.fallback');
});


Route::get('/employers/primary/json-data', 'ApiController@jsonData');
