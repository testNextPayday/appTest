<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class PendingLenderMiddleware
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
        if(Auth::guest()) {
            return redirect()->route('login');
        }
        
        if(!Auth::guard('web')->user()->is_lender && !Auth::guard('web')->user()->is_borrower) {
            return redirect()->route('users.profile.lender.activation'); 
        } else {
            return $next($request);    
        }
    }
}
