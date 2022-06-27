<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderFileClearance
{

    public function handle($request, Closure $next) {
        
        // Show :: Show Files permission doesn't affect showing lists
        if ($request->is('purchase-orders/files/index')) {
          if (Auth::user()->hasPermissionTo('Show Purchase Order Files')) {
              return $next($request);
          } else {
              if (Auth::user()->hasAnyPermission('Create Purchase Order Files',
                                                 'Edit Purchase Order Files',
                                                 'Delete Purchase Order Files')) {
                  return $next($request);
              } else {
                  abort('403');
              }
          }
        }

        // View
        if ($request->is('purchase-orders/files/view')) {
          if (!Auth::user()->hasPermissionTo('View Purchase Order Files')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Create
        if ($request->is('purchase-orders/files/create') ||
            $request->is('purchase-orders/files/store') ||
            $request->is('purchase-orders/files/store-ajax')) {
          if (!Auth::user()->hasPermissionTo('Create Purchase Order Files')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Edit
        if ($request->is('purchase-orders/files/*/edit') || $request->is('purchase-orders/files/*/update')) {
          if (!Auth::user()->hasPermissionTo('Edit Purchase Order Files')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        // Delete
        if ($request->is('purchase-orders/files/*/trash') || $request->is('purchase-orders/files/*/delete')) {
          if (!Auth::user()->hasPermissionTo('Delete Purchase Order Files')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        return $next($request);
    }
}
