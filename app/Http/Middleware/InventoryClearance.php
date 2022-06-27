<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class InventoryClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // INVENTORY ADMIN & USERS
              // Show :: Show Inventory permission doesn't affect showing lists
              if ($request->is('inventories')) {
                  if (Auth::user()->hasPermissionTo('Show Inventories')) {
                      return $next($request);
                  } else {
                      if (Auth::user()->hasAnyPermission('Create Inventories',
                                                         'Show Inventory Breakdown',
                                                         'Show Inventory Discrepancies',
                                                         'Delete Inventories',
                                                         'Get Inventory Raw Files',
                                                         'Import Inventories',
                                                         'View Inventories')) {
                          return $next($request);
                      } else {
                          abort('403');
                      }
                  }
              }

            // INVENTORY ADMIN
              // Show Breakdown - Inventory Admin & User
              if ($request->is('inventories/*/breakdown_view')) {
                  if (!Auth::user()->hasPermissionTo('Show Inventory Breakdown')) {
                      abort('403');
                  } else {
                      return $next($request);
                  }
              }

              // Show Discrepancy - Inventory Admin & User
              if ($request->is('inventories/*/discrepancy')) {
                  if (!Auth::user()->hasPermissionTo('Show Inventory Discrepancies')) {
                      abort('403');
                  } else {
                      return $next($request);
                  }
              }

              // Create - Inventory Admin only
              if ($request->is('inventories/create') || $request->is('inventories/store')) {
                  if (Auth::user()->branch->machine_number !== 103) {
                      abort('403');
                  } else {
                      if (!Auth::user()->hasPermissionTo('Create Inventories')) {
                          abort('403');
                      } else {
                          return $next($request);
                      }
                  }
              }

              // Delete - Inventory Admin only
              if ($request->is('inventories/*/trash') || $request->is('inventories/*/delete')) {
                  if (Auth::user()->branch->machine_number !== 103) {
                    abort('403');
                  } else {
                      if (!Auth::user()->hasPermissionTo('Delete Inventories')) {
                          abort('403');
                      } else {
                          return $next($request);
                      }
                  }

              }

            // INVENTORY USERS
              // View - Inventory Admin & User
              if ($request->is('inventories/*/view')) {
                  if (!Auth::user()->hasPermissionTo('View Inventories')) {
                      abort('403');
                  } else {
                      return $next($request);
                  }
              }

              // Get Raw - Inventory Admin & User
              if ($request->is('inventories/*/get_raw')) {
                  if (!Auth::user()->hasAnyPermission('Get Inventory Raw Files', 'Import Inventories')) {
                      abort('403');
                  } else {
                      return $next($request);
                  }
              }

              // Import - Inventory User only
              if ($request->is('inventories/*/import_branch') || $request->is('inventories/*/import_proceed')) {
                  if (Auth::user()->branch->machine_number === 103) {
                      abort('403');
                  } else {
                      if (!Auth::user()->hasPermissionTo('Import Inventories')) {
                          abort('403');
                      } else {
                          return $next($request);
                      }
                  }
              }
        }

        return $next($request);
    }
}
