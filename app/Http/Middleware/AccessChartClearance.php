<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AccessChartClearance
{

    public function handle($request, Closure $next) {
        // Show :: Show Access Charts permission doesn't affect showing lists
        if ($request->is('access_charts/index')) {
          if (Auth::user()->hasPermissionTo('Show Access Charts')) {
              return $next($request);
          } else {
              if (Auth::user()->hasAnyPermission('Create Access Charts',
                                                 'Edit Access Charts',
                                                 'Delete Access Charts',
                                                 'Show Approving Officers',
                                                 'Assign Approving Officers')) {
                  return $next($request);
              } else {
                  abort('403');
              }
          }
        }

        // Create
        if ($request->is('access_charts/create') || $request->is('access_charts/store')) {
          if (!Auth::user()->hasPermissionTo('Create Access Charts')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Edit
        if ($request->is('access_charts/*/edit') || $request->is('access_charts/*/update')) {
          if (!Auth::user()->hasPermissionTo('Edit Access Charts')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Delete
        if ($request->is('access_charts/*/trash') || $request->is('access_charts/*/delete')) {
          if (!Auth::user()->hasPermissionTo('Delete Access Charts')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Show :: Show Approving Officers permission doesn't affect showing lists
        if ($request->is('access_charts/*/officers')) {
          if (Auth::user()->hasPermissionTo('Show Approving Officers')) {
              return $next($request);
          } else {
              if (Auth::user()->hasAnyPermission('Assign Approving Officers')) {
                  return $next($request);
              } else {
                  abort('403');
              }
          }
        }

        // Assign Approving Officer
        if ($request->is('access_chart_users/*/store') ||
            $request->is('access_chart_users/*/edit') ||
            $request->is('access_chart_users/*/update') ||
            $request->is('access_levels/*/edit') ||
            $request->is('access_chart_users/*/trash') ||
            $request->is('access_chart_users/*/delete')) {
          if (!Auth::user()->hasPermissionTo('Assign Approving Officers')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        return $next($request);
    }
}
