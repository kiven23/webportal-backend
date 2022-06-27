<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ProductItemClearance {
    public function handle($request, Closure $next) {
      if (Auth::check()) {
        // Show :: Show Product Items permission doesn't affect showing lists
        if ($request->route()->getName() === 'product.items') {
          if (Auth::user()->hasPermissionTo('Show Product Items')) {
            return $next($request);
          } else {
            if (Auth::user()->hasAnyPermission('Create Product Items',
                                               'Edit Product Items',
                                               'Delete Product Items',
                                               'Show Computerware Tickets',
                                               'Create Computerware Tickets',
                                               'Edit Computerware Tickets',
                                               'Delete Computerware Tickets')) {
              return $next($request);
            } else {
              return response()->json('Forbidden', 403);
            }
          }
        }

        // Create/Store
        if ($request->route()->getName() === 'product.item.store') {
          if (!Auth::user()->hasPermissionTo('Create Product Items')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Edit/Update
        if ($request->route()->getName() === 'product.item.update') {
          if (!Auth::user()->hasPermissionTo('Edit Product Items')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Delete
        if ($request->route()->getName() === 'product.item.delete_multiple') {
          if (!Auth::user()->hasPermissionTo('Delete Product Items')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }
      }

      return $next($request);
    }
}
