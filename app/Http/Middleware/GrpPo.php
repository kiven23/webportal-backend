<?php

namespace App\Http\Middleware;

use Closure;

class GrpPo
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
       // Show Agency
       if ($request->is('api/inventory/grpo/search') 
          || $request->is('api/inventory/grpo/progress')
          || $request->is('api/inventory/grpo/create') || $request->is('api/inventory/grpo/createpo')
           ) {
        if (\Auth::user()->hasRole(['User GRPO'])) {
            return $next($request);
        } else {
            abort('403');
        }
    }
    }
}
