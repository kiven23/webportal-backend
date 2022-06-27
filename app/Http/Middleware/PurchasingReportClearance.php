<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PurchasingReportClearance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Show :: Show Purchasing Reports permission doesn't affect showing lists
        if ($request->is('purchasing-reports/index') || $request->is('purchasing-reports/*/subreport')) {
          if (Auth::user()->hasPermissionTo('Show Purchasing Reports')) {
              return $next($request);
          } else {
              if (Auth::user()->hasAnyPermission('Create Purchasing Reports',
                                                'Edit Purchasing Reports',
                                                'Delete Purchasing Reports')) {
                  return $next($request);
              } else {
                  abort('403');
              }
          }
        }

        // View
        if ($request->is('purchasing-reports/view') || $request->is('purchasing-reports/*/view-subreport')) {
          if (Auth::user()->hasPermissionTo('View Purchasing Reports')) {
              return $next($request);
          } else {
            abort('403');
          }
        }

        // Create
        if ($request->is('purchasing-reports/create') || $request->is('purchasing-reports/store')) {
          if (!Auth::user()->hasPermissionTo('Create Purchasing Reports')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Edit
        if ($request->is('purchasing-reports/*/edit') || $request->is('purchasing-reports/*/update')) {
          if (!Auth::user()->hasPermissionTo('Edit Purchasing Reports')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Delete
        if ($request->is('purchasing-reports/*/trash') || $request->is('purchasing-reports/*/delete')) {
          if (!Auth::user()->hasPermissionTo('Delete Purchasing Reports')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        return $next($request);
    }
}
