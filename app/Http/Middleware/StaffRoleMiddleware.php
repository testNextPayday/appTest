<?php

namespace App\Http\Middleware;

use Closure, Auth;

class StaffRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $staff = auth('staff')->user();
        
        if (!$staff) {
            return redirect()->route('staff.login')->with('failure', 'Please login first');
        }
        
        if ($staff->manages($roles[0])) {
            return $next($request);
        }
        
        else 
            return redirect()->back()->with('failure', 'You do not have permission for this action');
        return $next($request);
    }
}
