<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ConcernCategoryClearance
{
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Show :: Show Concern Categories permission doesn't affect showing lists
            if ($request->is('concerns/categories/index')) {
                if (Auth::user()->hasPermissionTo('Show Concern Categories')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasAnyPermission('Create Concern Categories',
                                                       'Edit Concern Categories',
                                                       'Delete Concern Categories')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            // Create
            if ($request->is('concerns/categories/create') || $request->is('concerns/categories/*/store')) {
                if (!Auth::user()->hasPermissionTo('Create Concern Categories')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }

            // Edit
            if ($request->is('concerns/categories/*/edit') || $request->is('concerns/categories/*/update')) {
                if (!Auth::user()->hasPermissionTo('Edit Concern Categories')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }

            // Delete
            if ($request->is('concerns/categories/*/trash') || $request->is('concerns/categories/*/delete')) {
                if (!Auth::user()->hasPermissionTo('Delete Concern Categories')) {
                    abort('403');
                } else {
                    return $next($request);
                }
            }
        }

        return $next($request);
    }
}
