<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ServiceProviderClearance {

    public function handle($request, Closure $next) {
      if (Auth::check()) {
        // Show :: Show Service Provider permission doesn't affect showing lists
        if ($request->route()->getName() === 'service.providers') {
          if (Auth::user()->hasPermissionTo('Show Service Providers')) {
            return $next($request);
          } else {
            if (Auth::user()->hasAnyPermission('Create Service Providers',
                                               'Edit Service Providers',
                                               'Delete Service Providers',
                                               'Show Connectivity Tickets',
                                               'Create Connectivity Tickets',
                                               'Edit Connectivity Tickets',
                                               'Delete Connectivity Tickets')) {
              return $next($request);
            } else {
              return response()->json('Forbidden', 403);
            }
          }
        }

        // Create/Store
        if ($request->route()->getName() === 'service.type.store') {
          if (!Auth::user()->hasPermissionTo('Create Service Providers')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Edit/Update
        if ($request->route()->getName() === 'service.type.update') {
          if (!Auth::user()->hasPermissionTo('Edit Service Providers')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Delete
        if ($request->route()->getName() === 'service.type.delete') {
          if (!Auth::user()->hasPermissionTo('Delete Service Providers')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }
      }

      return $next($request);
    }
}
