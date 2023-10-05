<?php

namespace App\Http\Middleware;

use Closure;

class JointMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {   
        $canAccess = false;
        foreach($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                $canAccess = true;
            }
        }
        
        return $canAccess ? $next($request) : redirect()->back();
    }
}
