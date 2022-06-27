<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PowerInterruptionClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Show :: Show Product Brands permission doesn't affect showing lists
            if ($request->is('power_interruption/index')) {
                if (Auth::user()->hasPermissionTo('Show Power Interruptions')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasAnyPermission('Create Power Interruptions',
                                                       'Edit Power Interruptions',
                                                       'Delete Power Interruptions')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            // Create
            if ($request->is('power_interruption/create') || $request->is('power_interruption/store')) {
                if (!Auth::user()->hasPermissionTo('Create Power Interruptions')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }

            // Edit
            if ($request->is('power_interruption/*/edit') || $request->is('power_interruption/*/update')) {
                if (!Auth::user()->hasPermissionTo('Edit Power Interruptions')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }

            // Delete
            if ($request->is('power_interruption/*/trash') || $request->is('power_interruption/*/delete')) {
                if (!Auth::user()->hasPermissionTo('Delete Power Interruptions')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }
        }

        return $next($request);
    }
}
