<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CompanyClearance
{

    public function handle($request, Closure $next) {

        // Show :: Show Companies permission doesn't affect showing lists
        if ($request->is('companies/index')) {
          if (Auth::user()->hasPermissionTo('Show Companies')) {
              return $next($request);
          } else {
              if (Auth::user()->hasAnyPermission('Create Companies',
                                                 'Edit Companies',
                                                 'Delete Companies')) {
                  return $next($request);
              } else {
                  abort('403');
              }
          }
        }

        // Create
        if ($request->is('companies/create') ||
            $request->is('companies/store') ||
            $request->is('companies/store-ajax')) {
          if (!Auth::user()->hasPermissionTo('Create Companies')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Edit
        if ($request->is('companies/*/edit') || $request->is('companies/*/update')) {
          if (!Auth::user()->hasPermissionTo('Edit Companies')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Delete
        if ($request->is('companies/*/trash') || $request->is('companies/*/delete')) {
          if (!Auth::user()->hasPermissionTo('Delete Companies')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        return $next($request);
    }
}
