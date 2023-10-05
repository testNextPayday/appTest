<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        if ($exception instanceof \Illuminate\Auth\AuthenticationException && Arr::get($exception->guards(), 0) == 'api') {
            return response()->json(['status'=>false,'message'=>' Unauthorised User']);
        }
         $class = get_class($exception);

         switch($class) {

             case 'Illuminate\Auth\AuthenticationException':

                $guard = Arr::get($exception->guards(), 0);

                 switch ($guard) {
                    case 'admin':
                         $login = 'admin.login';
                    break;

                    case 'staff':
                         $login = 'staff.login';
                    break;

                    case 'investor':
                         $login = 'investors.login';
                    break;

                    case 'affiliate':
                        $login = 'affiliates.login';
                    break;

                    default:

                        // if( app()->environment() == 'production') {
                        //     // i want to do this only when we are in production
                        //     return redirect()->away("https://nextpayday.ng/login");
                        // }
                        $login = 'login';
                    break;
                     
                 }

            return redirect()->route($login);
             
         }
        return parent::render($request, $exception);
    }
}
