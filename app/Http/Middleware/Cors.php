<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() == "OPTIONS") {
            return response('');
        }

        //$allowedOrigins = config('unicredit.allowed_origins');

        
        //if ($origin = $request->server('HTTP_ORIGIN')) {

            //if (in_array($origin, $allowedOrigins)) {

                return $next($request)
                    ->header("Access-Control-Allow-Origin", "*")
                    ->header('Access-Control-Allow-Credentials', true)
                    ->header('Accept', 'application/json')
                    ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
                    ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
            //}
        //}

        //return $next($request);
        
    }
}
