<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ComputerwareTicketClearance {
    public function handle($request, Closure $next) {
      if (Auth::check()) {
        // Show :: Show Computerware Tickets doesn't affect showing lists
        if ($request->route()->getName() === 'tickets.computerwares') {
          if (Auth::user()->hasPermissionTo('Show Computerware Tickets')) {
            return $next($request);
          } else {
            if (Auth::user()->hasAnyPermission('Create Computerware Tickets',
                                               'Edit Computerware Tickets',
                                               'Delete Computerware Tickets')) {
              return $next($request);
            } else {
              return response()->json('Forbidden', 403);
            }
          }
        }

        // Create
        if ($request->route()->getName() === 'tickets.computerware.store') {
          if (!Auth::user()->hasPermissionTo('Create Computerware Tickets')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Edit
        if ($request->route()->getName() === 'tickets.computerware.update') {
          if (!Auth::user()->hasPermissionTo('Edit Computerware Tickets')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }

        // Delete
        if ($request->route()->getName() === 'tickets.computerware.delete') {
          if (!Auth::user()->hasPermissionTo('Delete Computerware Tickets')) {
            return response()->json('Forbidden', 418);
          } else {
            return $next($request);
          }
        }
      }

      return $next($request);
    }
}
