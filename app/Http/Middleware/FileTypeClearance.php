<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class FileTypeClearance
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
      // Show :: Show File Types permission doesn't affect showing lists
      if ($request->is('file-types/index')) {
        if (Auth::user()->hasPermissionTo('Show File Types')) {
            return $next($request);
        } else {
            if (Auth::user()->hasAnyPermission('Create File Types',
                                              'Edit File Types',
                                              'Delete File Types')) {
                return $next($request);
            } else {
                abort('403');
            }
        }
      }

      // Create
      if ($request->is('file-types/create') || $request->is('file-types/store')) {
        if (!Auth::user()->hasPermissionTo('Create File Types')) {
            abort('403');
        } else {
            return $next($request);
        }
      }

      // Edit
      if ($request->is('file-types/*/edit') || $request->is('file-types/*/update')) {
        if (!Auth::user()->hasPermissionTo('Edit File Types')) {
            abort('403');
        } else {
            return $next($request);
        }
      }

      // Delete
      if ($request->is('file-types/*/trash') || $request->is('file-types/*/delete')) {
        if (!Auth::user()->hasPermissionTo('Delete File Types')) {
            abort('403');
        } else {
            return $next($request);
        }
      }

          return $next($request);
    }
}
