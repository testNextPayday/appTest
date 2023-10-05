<?php

namespace App\Providers;


use App\Models\BankDetail;
use App\Models\PromissoryNote;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Models\GatewayTransaction;
use App\Models\BillApprovalRequest;
use App\Models\PromissoryNoteSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();

        Route::model('gateway', GatewayTransaction::class);

        Route::model('bankdetail', BankDetail::class);

        Route::model('request_id', WithdrawalRequest::class);

        Route::model('wallettransaction', WalletTransaction::class);

        Route::model('promissory_note', PromissoryNote::class);

        Route::model('note_setting', PromissoryNoteSetting::class);

        Route::model('bill_request', BillApprovalRequest::class);
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
        $this->mapAffiliateWebRoutes();
        $this->mapAdminWebRoutes();
        $this->mapStaffWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }
    
    /**
     * Define the "affiliate web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAffiliateWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace . "\Affiliates")
             ->prefix('affiliates')
             ->group(base_path('routes/web/affiliates.php'));
    }
    
    /**
     * Define the "admin web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace . "\Admin")
             ->prefix('ucnull')
             ->group(base_path('routes/web/admin.php'));
    }
    
    
    /**
     * Define the "staff web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapStaffWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace . "\Staff")
             ->prefix('staff')
             ->group(base_path('routes/web/staff.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
