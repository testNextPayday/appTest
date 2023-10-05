<?php

namespace App\Http\Middleware;

use Closure;

class AffiliateStatus
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
        $affiliate = auth('affiliate')->user();
        
        if (!$affiliate)
            return redirect()->route('affiliates.login');
            
        if (!$affiliate->verified_at)
            // Awaiting verification. Take somewhere
            return redirect()->route('affiliates.waiting-area', ['condition' => 'unverified']);
        if (!$affiliate->status)
            // Deactivated.
            return redirect()->route('affiliates.waiting-area', ['condition' => 'deactivated']);
        
        return $next($request);    
    }
}
