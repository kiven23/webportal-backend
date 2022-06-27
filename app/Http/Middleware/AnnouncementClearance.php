<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AnnouncementClearance
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
        // Show :: Show Announcements permission doesn't affect showing lists
		if ($request->is('announcements/index')) {
            if (Auth::user()->hasPermissionTo('Show Announcements')) {
                return $next($request);
            } else {
                if (Auth::user()->hasAnyPermission('Create Announcements',
                                                   'Edit Announcements',
                                                   'Delete Announcements')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
          }

          // View
          if ($request->is('announcements/view') || $request->is('announcements/view')) {
            if (!Auth::user()->hasPermissionTo('View Announcements')) {
                abort('403');
            } else {
                return $next($request);
            }
          }
  
          // Create
          if ($request->is('announcements/create') || $request->is('announcements/store')) {
            if (!Auth::user()->hasPermissionTo('Create Announcements')) {
                abort('403');
            } else {
                return $next($request);
            }
          }
  
          // Edit
          if ($request->is('announcements/*/edit') || $request->is('announcements/*/update')) {
            if (!Auth::user()->hasPermissionTo('Edit Announcements')) {
                abort('403');
            } else {
                return $next($request);
            }
          }
  
          // Delete
          if ($request->is('announcements/*/trash') || $request->is('announcements/*/delete')) {
            if (!Auth::user()->hasPermissionTo('Delete Announcements')) {
                abort('403');
            } else {
                return $next($request);
            }
          }
  
          return $next($request);
    }
}
