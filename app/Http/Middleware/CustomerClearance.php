<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CustomerClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Start of Customers
            // Show :: Show Customers permission doesn't affect showing & printing lists
            if ($request->is('customers') ||
                $request->is('customers/*/printimage') ||
                $request->is('customers/*/printimage2') ||
                $request->is('customers/*/printimage3')) {
                if (Auth::user()->hasPermissionTo('Show Customers')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasAnyPermission('Create Customers',
                                                       'Edit Customers',
                                                       'Delete Customers',
                                                       'Show Customer Files',
                                                       'Create Customer Files',
                                                       'Edit Customer Files',
                                                       'Delete Customer Files')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            // Create
            if ($request->is('customers/basic')) {
                if (Auth::user()->hasPermissionTo('Create Customers')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Edit
            if ($request->is('customers/*/edit') || $request->is('customers/*/update')) {
                if (Auth::user()->hasPermissionTo('Edit Customers')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Delete
            if ($request->is('customers/*/trash') || $request->is('customers/*/delete')) {
                if (Auth::user()->hasPermissionTo('Delete Customers')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Import
            if ($request->is('customers/import') || $request->is('customers/*/import_proceed')) {
                if (Auth::user()->hasPermissionTo('Import Customers')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
            // End of Customers

            // Start of Customer Files
            // Show :: Show Customers permission doesn't affect showing & printing lists
            if ($request->is('customers/*/files')) {
                if (Auth::user()->hasPermissionTo('Show Customer Files')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasAnyPermission('Create Customer Files',
                                                       'Edit Customer Files',
                                                       'Delete Customer Files')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            // Create
            if ($request->is('customers/*/files/add') || $request->is('customers/*/files/supply')) {
                if (Auth::user()->hasPermissionTo('Create Customer Files')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Edit
            if ($request->is('customers/*/files/*/alter') || $request->is('customers/*/files/*/revise')) {
                if (Auth::user()->hasPermissionTo('Edit Customer Files')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Delete
            if ($request->is('customers/*/files/*/bin') || $request->is('customers/*/files/*/destroy')) {
                if (Auth::user()->hasPermissionTo('Delete Customer Files')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
            // End of Customer Files
        }

        return $next($request);
    }
}
