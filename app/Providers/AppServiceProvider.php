<?php

namespace App\Providers;

use Validator;
use App\Models\Loan;

use GuzzleHttp\Client;
use App\Facades\BulkSms;
use App\Models\Investor;

use App\Models\Affiliate;
use App\Models\LoanRequest;
use App\Facades\WhatsappSMS;
use App\Models\RepaymentPlan;
use App\Repositories\Sharenet;
use App\Observers\LoanObserver;
use App\Unicredit\Fakes\FakeSms;
use App\Repositories\SmsInterface;
use Illuminate\Support\Facades\DB;
use App\Observers\InvestorObserver;
use App\Repositories\SmsRepository;
use App\Observers\AffiliateObserver;
use Illuminate\Support\Facades\File;
use Unicodeveloper\Paystack\Paystack;
use App\Observers\LoanRequestObserver;
use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Support\Facades\Schema;
use App\Repositories\WhatsappInterface;
use Illuminate\Support\ServiceProvider;
use App\Observers\RepaymentPlanObserver;
use App\Unicredit\Contracts\MoneySender;
use App\Unicredit\Collection\CardService;
use App\Unicredit\Fakes\FakeBankStatement;
use App\Unicredit\Contracts\PaymentGateway;
use App\Unicredit\Contracts\CustomerRepository;
use App\Unicredit\Payments\PaystackMoneySender;
use App\Repositories\PaystackCustomerRepository;
use App\Repositories\Models\Redis\LoanRepository;
use App\Unicredit\Contracts\Models\ILoanRepository;
use App\Services\BankStatement\BankStatementService;
use App\Services\BankStatement\IBankStatementService;
use App\Repositories\Models\Redis\AffiliateLoanRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    
        if (env('APP_DEBUG')) {
           
            DB::listen(function($query){
                
                File::append(
                    storage_path('/logs/query.log'),
                    'NEW REQUEST'.PHP_EOL.$query->sql.' ['.implode(' ,', $query->bindings). '] took '.$query->time.'ms '.PHP_EOL
                );
            });

        }
        
        Affiliate::observe(AffiliateObserver::class);
        LoanRequest::observe(LoanRequestObserver::class);
        Loan::observe(LoanObserver::class);

        Investor::observe(InvestorObserver::class);

        RepaymentPlan::observe(RepaymentPlanObserver::class);
        
        Validator::extend('image64', function ($attribute, $value, $parameters, $validator) {
            $image = base64_decode(explode(',', $value)[1]);
            $f = finfo_open();
            $mimeType = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
            if (in_array($mimeType, $parameters)) {
                return true;
            }
            return false;
        });

        Validator::replacer('image64', function($message, $attribute, $rule, $parameters) {
            return str_replace(':values',join(",",$parameters),$message);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind(SmsRepository::class, Sharenet::class);

        $evironment = $this->app->environment();

        if ($evironment == 'staging' || $evironment == 'local') {

            $this->app->bind(SmsInterface::class, FakeSms::class);
            $this->app->bind(IBankStatementService::class, FakeBankStatement::class);

        }else{

            $this->app->bind(SmsInterface::class, BulkSms::class);
            $this->app->bind(IBankStatementService::class, BankStatementService::class);
        }


       $this->app->bind(MoneySender::class, PaystackMoneySender::class);

       $this->app->bind(WhatsappInterface::class, WhatsappSMS::class);

       $this->app->bind(PaymentGateway::class, Paystack::class);

       $this->app->bind(ILoanRepository::class, LoanRepository::class);

       $this->app->when('App\Http\Controllers\Affiliates\LoanController')
            ->needs(ILoanRepository::class)
            ->give(function(){
                return new AffiliateLoanRepository();
            });

        $this->app->bind(
           CustomerRepository::class, PaystackCustomerRepository::class
        );

       
    }
}
