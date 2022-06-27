<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ServiceTypeClearance {

    public function handle($request, Closure $next) {
      if (Auth::check()) {
        // Show :: Show Service Type permission doesn't affect showing lists
        if ($request->route()->getName() === 'service.types') {
          if (Auth::user()->hasPermissionTo('Show Service Types')) {
            return $next($request);
          } else {
            if (Auth::user()->hasAnyPermission('Create Service Types',
                                               'Edit Service Types',
                                               'Delete Service Types',
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
          if (!Auth::user()->hasPermissionTo('Create Service Types')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Edit/Update
        if ($request->route()->getName() === 'service.type.update') {
          if (!Auth::user()->hasPermissionTo('Edit Service Types')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Delete
        if ($request->route()->getName() === 'service.type.delete') {
          if (!Auth::user()->hasPermissionTo('Delete Service Types')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }
      }

      return $next($request);
    }
}
