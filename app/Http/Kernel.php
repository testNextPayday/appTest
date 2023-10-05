<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Fruitcake\Cors\HandleCors::class,
            'throttle:60,1',
            'bindings'
            
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'lender' => \App\Http\Middleware\LenderMiddleware::class,
        'joint' => \App\Http\Middleware\JointMiddleware::class,
        'lender.pending' => \App\Http\Middleware\PendingLenderMiddleware::class,
        'unverified.investor' => \App\Http\Middleware\UnverifiedInvestorMiddleware::class,
        'unverified.email' => \App\Http\Middleware\UnverifiedEmailMiddleware::class,
        'staff.roles' => \App\Http\Middleware\StaffRoleMiddleware::class,
        'staff.roles.investor' => \App\Http\Middleware\StaffRoleInvestor::class,
        'staff.roles.borrower' => \App\Http\Middleware\StaffRoleBorrower::class,
        'staff.roles.repayments' => \App\Http\Middleware\StaffRepaymentUpload::class,
        'affiliate-status' => \App\Http\Middleware\AffiliateStatus::class,
        
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,

        'notification'=>\App\Http\Middleware\ReadNotifications::class
    ];
}
