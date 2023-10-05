<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class LenderMiddleware
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
        
        if(Auth::guard('web')->user()->is_lender) {
            return $next($request);    
        } else {
            return redirect()->back();   
        }
    }
}
