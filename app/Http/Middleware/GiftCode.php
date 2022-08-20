<?php

namespace App\Http\Middleware;

use Closure;

class GiftCode
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
        return $next($request);
                // Show Gift Code
                if ($request->is('api/authorized/giftcode')) {
                    if (\Auth::user()->hasRole(['Gift Code Terminal'])) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
    }
}
