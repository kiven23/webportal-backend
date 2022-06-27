<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ConnectivityTicketClearance {

    public function handle($request, Closure $next) {
      if (Auth::check()) {
        // Show :: Show Connectivity Tickets doesn't affect showing lists
        if ($request->route()->getName() === 'tickets.connectivities') {
          if (Auth::user()->hasPermissionTo('Show Connectivity Tickets')) {
            return $next($request);
          } else {
            if (Auth::user()->hasAnyPermission('Create Connectivity Tickets',
                                               'Edit Connectivity Tickets',
                                               'Delete Connectivity Tickets')) {
              return $next($request);
            } else {
              return response()->json('Forbidden', 403);
            }
          }
        }

        // Create
        if ($request->route()->getName() === 'tickets.Connectivity.store') {
          if (!Auth::user()->hasPermissionTo('Create Connectivity Tickets')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Edit
        if ($request->route()->getName() === 'tickets.Connectivity.update') {
          if (!Auth::user()->hasPermissionTo('Edit Connectivity Tickets')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Delete
        if ($request->route()->getName() === 'tickets.Connectivity.delete') {
          if (!Auth::user()->hasPermissionTo('Delete Connectivity Tickets')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }
      }

      return $next($request);
    }
}
