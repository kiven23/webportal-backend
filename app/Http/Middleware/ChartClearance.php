<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ChartClearance {

    public function handle($request, Closure $next) {
        
        return $next($request);
    }
}
