<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ConcernClearance
{
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Show :: Show Concern permission doesn't affect showing lists
            if ($request->is('concerns/index')) {
                if (Auth::user()->hasPermissionTo('Show Concerns')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasAnyPermission('Create Concerns',
                                                       'Edit Concerns',
                                                       'Delete Concerns')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            // Create
            if ($request->is('concerns/create') || $request->is('concerns/*/store')) {
                if (!Auth::user()->hasPermissionTo('Create Concerns')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }

            // Edit
            if ($request->is('concerns/*/edit') || $request->is('concerns/*/update')) {
                if (!Auth::user()->hasPermissionTo('Edit Concerns')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }

            // Delete
            if ($request->is('concerns/*/trash') || $request->is('concerns/*/delete')) {
                if (!Auth::user()->hasPermissionTo('Delete Concerns')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }
        }

        return $next($request);
    }
}
