<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class OvertimeClearance
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
        if (Auth::check()) {
            // Show - Overtime
            if ($request->is('overtimes')) {
                if (Auth::user()->hasAnyPermission(['Show Overtimes',
                                                    'Create Overtimes',
                                                    'Edit Overtimes'])) {
                    return $next($request);
                } else {
                    return abort('403');
                }
            }

            // Create - Overtime
            if ($request->is('overtimes/create') || $request->is('overtimes/store')) {
                if (Auth::user()->hasPermissionTo('Create Overtimes')) {
                    return $next($request);
                } else {
                    return abort('403');
                }
            }

            // Edit - Overtime
            if ($request->is('overtimes/*/edit') || $request->is('overtimes/*/update')) {
                if (Auth::user()->hasPermissionTo('Edit Overtimes')) {
                    return $next($request);
                } else {
                    return abort('403');
                }
            }

            // Delete - Overtime
            if ($request->is('overtimes/*/trash') || $request->is('overtimes/*/delete')) {
                if (Auth::user()->hasPermissionTo('Delete Overtimes')) {
                    return $next($request);
                } else {
                    return abort('403');
                }
            }
        }

        return $next($request);
    }
}
