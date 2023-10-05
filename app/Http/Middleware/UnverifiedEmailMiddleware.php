<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class UnverifiedEmailMiddleware
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
        if(Auth::guard('web')->guest() && Auth::guard('investor')->guest()) {
            return redirect()->route('login');
        }
        
        $guard = Auth::guard('investor')->check() ? 'investor' : 'web';
        
        if(Auth::guard($guard)->user()->email_verified) {
            return $next($request);    
        } else {
            return redirect()->route('email.unverified');   
        }
    }
}
