<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ConcernTypeClearance
{
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Show :: Show Concern Types permission doesn't affect showing lists
            if ($request->is('concerns/types/index')) {
                if (Auth::user()->hasPermissionTo('Show Concern Types')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasAnyPermission('Create Concern Types',
                                                       'Edit Concern Types',
                                                       'Delete Concern Types')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            // Create
            if ($request->is('concerns/types/create') || $request->is('concerns/types/*/store')) {
                if (!Auth::user()->hasPermissionTo('Create Concern Types')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }

            // Edit
            if ($request->is('concerns/types/*/edit') || $request->is('concerns/types/*/update')) {
                if (!Auth::user()->hasPermissionTo('Edit Concern Types')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }

            // Delete
            if ($request->is('concerns/types/*/trash') || $request->is('concerns/types/*/delete')) {
                if (!Auth::user()->hasPermissionTo('Delete Concern Types')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }
        }

        return $next($request);
    }
}
