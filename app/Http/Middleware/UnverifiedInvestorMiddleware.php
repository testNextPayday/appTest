<?php

namespace App\Http\Middleware;

use Closure;

class UnverifiedInvestorMiddleware
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
        if(auth()->guard('investor')->guest()) {
            return redirect()->route('investors.login');
        }
        
        // if(!auth()->guard('investor')->user()->is_verified) {
        //     return redirect()->route('investors.verification'); 
        // } else {
        //     return $next($request);    
        // }

        return $next($request);   
    }
}
