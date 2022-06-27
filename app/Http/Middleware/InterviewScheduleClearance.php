<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class InterviewScheduleClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Show :: Show Interview Schedule permission doesn't affect showing & printing lists
            if ($request->is('schedules/interviews/index')) {
                if (Auth::user()->hasPermissionTo('Show Interview Schedules')) {
                    return $next($request);
                } else {
                    if (Auth::user()->hasAnyPermission('Add Interview Schedules',
                                                       'Create Interview Schedules',
                                                       'Edit Interview Schedules',
                                                       'Delete Interview Schedules')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
            }

            // Add
            if ($request->is('schedules/interviews/add')) {
                if (Auth::user()->hasPermissionTo('Add Interview Schedules')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Create
            if ($request->is('schedules/interviews/create') || $request->is('schedules/interviews/store')) {
                if (Auth::user()->hasPermissionTo('Create Interview Schedules') ||
                    Auth::user()->hasPermissionTo('Add Interview Schedules')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Edit
            if ($request->is('schedules/interviews/*/edit') || $request->is('schedules/interviews/*/update')) {
                if (Auth::user()->hasPermissionTo('Edit Interview Schedules') ||
                    Auth::user()->hasPermissionTo('Add Interview Schedules')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }

            // Delete
            if ($request->is('schedules/interviews/*/trash') || $request->is('schedules/interviews/*/delete')) {
                if (Auth::user()->hasPermissionTo('Delete Interview Schedules') ||
                    Auth::user()->hasPermissionTo('Add Interview Schedules')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
        }
        return $next($request);
    }
}
