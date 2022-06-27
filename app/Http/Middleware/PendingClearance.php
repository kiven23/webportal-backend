<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\PowerInterruption;
use App\ConnectivityTicket;
use Carbon\Carbon;

class PendingClearance{

    public function handle($request, Closure $next) {
      // Show :: Show Pending Transaction permission doesn't affect showing lists
      if ($request->route()->getName() === 'pendingtransactions') {
        if (Auth::user()->hasPermissionTo('Show Pendings')) {
          return $next($request);
        } else {
          if (Auth::user()->hasAnyPermission('Create Pendings',
                                            'Edit Pendings',
                                            'Readd Pendings',
                                            'Access Pending Charts',
                                            'Show Pending Charts',
                                            'Delete Pendings')) {
            return $next($request);
          } else {
            return response()->json('Forbidden', 403);
          }
        }
      }

      // Create/Store
      if ($request->route()->getName() === 'pendingtransaction.store') {
        if (!Auth::user()->hasPermissionTo('Create Pendings')) {
          return response()->json('Forbidden', 418);
        } else {
          return $next($request);
        }
      }

      // Edit/Update
      if ($request->route()->getName() === 'pendingtransaction.update') {
        if (!Auth::user()->hasPermissionTo('Edit Pendings')) {
          return response()->json('Forbidden', 418);
        } else {
          return $next($request);
        }
      }

      // Delete
      if ($request->route()->getName() === 'pendingtransaction.delete') {
        if (!Auth::user()->hasPermissionTo('Delete Pendings')) {
          return response()->json('Forbidden', 418);
        } else {
          return $next($request);
        }
      }

      return $next($request);
    }
}
