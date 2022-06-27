<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ContactListClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Show :: Show Contact Lists permission doesn't affect showing lists
            if ($request->is('contact_lists/message_casts')) {
                if (Auth::user()->hasPermissionTo('Show Contact Lists')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasAnyPermission('Create Contact Lists',
                                                       'Edit Contact Lists',
                                                       'Delete Contact Lists')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            // Create Contact List
            if ($request->is('contact_lists/message_casts/create') || $request->is('contact_lists/message_casts/store')) {
                if (Auth::user()->hasPermissionTo('Create Contact Lists')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Edit Contact List
            if ($request->is('contact_lists/message_casts/*/edit') || $request->is('contact_lists/message_casts/*/update')) {
                if (Auth::user()->hasPermissionTo('Edit Contact Lists')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Delete Contact List
            if ($request->is('contact_lists/message_casts/*/trash') || $request->is('contact_lists/message_casts/*/delete')) {
                if (Auth::user()->hasPermissionTo('Delete Contact Lists')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
        }

        return $next($request);
    }
}
