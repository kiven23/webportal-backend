<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ProductBrandClearance {

    public function handle($request, Closure $next) {
      if (Auth::check()) {
        // Show :: Show Product Brands permission doesn't affect showing lists
        if ($request->route()->getName() === 'product.brands') {
          if (Auth::user()->hasPermissionTo('Show Product Brands')) {
            return $next($request);
          } else {
            if (Auth::user()->hasAnyPermission('Create Product Brands',
                                               'Edit Product Brands',
                                               'Delete Product Brands',
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
        if ($request->route()->getName() === 'product.brand.store') {
          if (!Auth::user()->hasPermissionTo('Create Product Brands')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Edit/Update
        if ($request->route()->getName() === 'product.brand.update') {
          if (!Auth::user()->hasPermissionTo('Edit Product Brands')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Delete
        if ($request->route()->getName() === 'product.brand.delete') {
          if (!Auth::user()->hasPermissionTo('Delete Product Brands')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }
      }

      return $next($request);
    }
}
