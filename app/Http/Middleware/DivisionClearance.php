<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DivisionClearance
{
    
    public function handle($request, Closure $next) {
      // Show :: Show Divisions permission doesn't affect showing lists
      if ($request->route()->getName() === 'divisions') {
        if (Auth::user()->hasPermissionTo('Show Divisions')) {
          return $next($request);
        } else {
          if (Auth::user()->hasAnyPermission('Create Divisions',
                                             'Edit Divisions',
                                             'Delete Divisions',
                                             'Show User Employments',
                                             'Create User Employments',
                                             'Edit User Employments',
                                             'Delete User Employments')) {
            return $next($request);
          } else {
            return response()->json('Forbidden', 403);
          }
        }
      }

      // Create/Store
      if ($request->route()->getName() === 'division.store') {
        if (!Auth::user()->hasPermissionTo('Create Divisions')) {
          return response()->json('Forbidden', 418);
        } else {
          return $next($request);
        }
      }

      // Edit/Update
      if ($request->route()->getName() === 'division.update') {
        if (!Auth::user()->hasPermissionTo('Edit Divisions')) {
          return response()->json('Forbidden', 418);
        } else {
          return $next($request);
        }
      }

      // Delete
      if ($request->route()->getName() === 'division.delete') {
        if (!Auth::user()->hasPermissionTo('Delete Divisions')) {
          return response()->json('Forbidden', 418);
        } else {
          return $next($request);
        }
      }

      return $next($request);
    }
}
