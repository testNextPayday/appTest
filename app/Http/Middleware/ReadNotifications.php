<?php

namespace App\Http\Middleware;

use Closure;

class ReadNotifications
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
       
        $notification = $request->user()
            ->notifications->where('id', $request->notification)->first();

       
        if ($notification) {

            $notification->markAsRead();
        }
        return $next($request);
    }
}
