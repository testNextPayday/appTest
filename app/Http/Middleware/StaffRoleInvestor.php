<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class StaffRoleInvestor
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
        $staff = auth('staff')->user();
        
        if (!$staff) {
            return redirect()->route('staff.login')->with('failure', 'You need to login first');
        }
        
        
        if ($staff->manages('investors'))
            return $next($request);
        else 
            return redirect()->back()->with('failure', 'You do not have permission for this action');
    }
}
