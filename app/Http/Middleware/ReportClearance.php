<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ReportClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Show :: Show Biometric Reports permission doesn't affect showing lists
            if ($request->is('reports/biometric')) {
                if (Auth::user()->hasPermissionTo('Generate Biometric Reports')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Show :: Show DTR Reports permission doesn't affect showing lists
            if ($request->is('reports/dtr')) {
                if (Auth::user()->hasPermissionTo('Generate DTR Reports')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Show :: Show Overtime Reports permission doesn't affect showing lists
            if ($request->is('reports/overtime')) {
                if (Auth::user()->hasPermissionTo('Generate Overtime Reports')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Import
            if ($request->is('reports/import')) {
                if (Auth::user()->hasPermissionTo('Import Daily Time Record Logs')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
        }

        return $next($request);
    }
}
