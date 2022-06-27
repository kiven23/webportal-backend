<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ProductCategoryClearance {

    public function handle($request, Closure $next) {
      if (Auth::check()) {
        // Show :: Show Product Categories permission doesn't affect showing lists
        if ($request->route()->getName() === 'product.categories') {
          if (Auth::user()->hasPermissionTo('Show Product Categories')) {
            return $next($request);
          } else {
            if (Auth::user()->hasAnyPermission('Create Product Categories',
                                               'Edit Product Categories',
                                               'Delete Product Categories',
                                               'Show Product Items',
                                               'Create Product Items',
                                               'Edit Product Items',
                                               'Delete Product Items')) {
              return $next($request);
            } else {
              return response()->json('Forbidden', 403);
            }
          }
        }

        // Create/Store
        if ($request->route()->getName() === 'product.category.store') {
          if (!Auth::user()->hasPermissionTo('Create Product Categories')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Edit/Update
        if ($request->route()->getName() === 'product.category.update') {
          if (!Auth::user()->hasPermissionTo('Edit Product Categories')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Delete
        if ($request->route()->getName() === 'product.category.delete') {
          if (!Auth::user()->hasPermissionTo('Delete Product Categories')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }
      }

      return $next($request);
    }
}
