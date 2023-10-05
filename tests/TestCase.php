<?php

namespace Tests;

use App\Models\User;
use App\Models\Admin;
use App\Models\Investor;

use App\Exceptions\Handler;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private $oldExceptionHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disableExceptionHandling();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    protected function signIn($user = null, $guard = null)
    {
        switch ($guard) {
            case 'investor':
                $user = $user ?: create(Investor::class);
                break;
            case 'admin':
                $user = $user ?: create(Admin::class);
                break;
            default:
                $user = $user ?: create(User::class);
        
        } 
        
        $this->actingAs($user, $guard);
        return $this;
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);
        return $this;
    }

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Throwable $e)
            {
            }
            public function render($request, \Throwable $e)
            {
                throw $e;
            }
        });
    }
}
