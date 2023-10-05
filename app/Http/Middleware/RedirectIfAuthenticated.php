<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
       
       
        switch ($guard) {
            case 'admin':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('admin.dashboard');
                }
                break;
                
            case 'staff':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('staff.dashboard');
                }
                break;
            
            case 'investor':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('investors.dashboard');
                }
                break;
                
            case 'affiliate':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('affiliates.dashboard');
                }
                break;
            case 'api':
                $user = Auth::guard('web')->loginUsingId($request->user('api')->id,true);
                if($user){
                    return $next($request);
                }
                
                break;
            default:

            if (Auth::guard($guard)->check()) {
                
                    return redirect('/');
            }
                    
                break;
            }

        return $next($request);
    }
}
