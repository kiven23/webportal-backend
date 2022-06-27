<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ServiceCategoryClearance {

    public function handle($request, Closure $next) {
      if (Auth::check()) {
        // Show :: Show Service Categories permission doesn't affect showing lists
        if ($request->route()->getName() === 'service.categories') {
          if (Auth::user()->hasPermissionTo('Show Service Categories')) {
            return $next($request);
          } else {
            if (Auth::user()->hasAnyPermission('Create Service Categories',
                                               'Edit Service Categories',
                                               'Delete Service Categories',
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
        if ($request->route()->getName() === 'service.category.store') {
          if (!Auth::user()->hasPermissionTo('Create Service Categories')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Edit/Update
        if ($request->route()->getName() === 'service.category.update') {
          if (!Auth::user()->hasPermissionTo('Edit Service Categories')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Delete
        if ($request->route()->getName() === 'service.category.delete') {
          if (!Auth::user()->hasPermissionTo('Delete Service Categories')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }
      }

      return $next($request);
    }
}
