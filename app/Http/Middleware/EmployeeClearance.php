<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EmployeeClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Show :: Show Inventory permission doesn't affect showing lists
            if ($request->is('employees/index')) {
                if (Auth::user()->hasPermissionTo('Show Employees')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasPermissionTo('Edit Employees')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            if ($request->is('employees/*/edit') || $request->is('employees/*/update')) {
                if (Auth::user()->hasPermissionTo('Edit Employees')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
        }

        return $next($request);
    }
}
