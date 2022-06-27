<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MaintRequestClearance
{
    public function handle($request, Closure $next) {
      // Show :: Show Maintenance Requests permission doesn't affect showing lists
      if ($request->is('maint-requests/index')) {
        if (Auth::user()->hasPermissionTo('Show Maintenance Requests')) {
            return $next($request);
        } else {
            if (Auth::user()->hasAnyPermission('Create Maintenance Requests',
                                               'Edit Maintenance Requests',
                                               'Delete Maintenance Requests')) {
                return $next($request);
            } else {
                abort('403');
            }
        }
      }

      // For viewing
      if ($request->is('maint-requests/*/view')) {
        if (Auth::user()->hasPermissionTo('Show Maintenance Requests')) {
            return $next($request);
        } else {
            if (Auth::user()->hasAnyPermission('Create Maintenance Requests',
                                               'Edit Maintenance Requests',
                                               'Delete Maintenance Requests',

                                               'Escalated Delete Maintenance Requests',
                                               'View Maintenance Requests',
                                               'Approve Maintenance Requests',
                                               'Cancel Maintenance Requests')) {
                return $next($request);
            } else {
                abort('403');
            }
        }
      }

      // Create
      if ($request->is('maint-requests/create') || $request->is('maint-requests/store')) {
        if (!Auth::user()->hasPermissionTo('Create Maintenance Requests')) {
            abort('403');
        } else {
            return $next($request);
        }
      }

      // Edit
      if ($request->is('maint-requests/*/edit') || $request->is('maint-requests/*/update')) {
        if (!Auth::user()->hasPermissionTo('Edit Maintenance Requests')) {
            abort('403');
        } else {
            return $next($request);
        }
      }

      // Delete
      if ($request->is('maint-requests/*/trash') || $request->is('maint-requests/*/delete')) {
        if (!Auth::user()->hasAnyPermission(['Delete Maintenance Requests',
                                             'Escalated Delete Maintenance Requests'])) {
            abort('403');
        } else {
            return $next($request);
        }
      }

      // All Lists
      if ($request->is('maint-requests/lists')) {
        if (!Auth::user()->hasPermissionTo('Maintenance Request Lists')) {
            abort('403');
        } else {
            return $next($request);
        }
      }

      return $next($request);
    }
}
