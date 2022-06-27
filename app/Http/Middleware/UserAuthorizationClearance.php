<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserAuthorizationClearance
{

    public function handle($request, Closure $next) {
        // Show :: Show User Authorizations permission doesn't affect showing lists
        if ($request->is('users/authorizations/index')) {
          if (Auth::user()->hasPermissionTo('Show User Authorizations')) {
              return $next($request);
          } else {
              if (Auth::user()->hasAnyPermission('Edit User Authorizations')) {
                  return $next($request);
              } else {
                  abort('403');
              }
          }
        }

        // Edit
        if ($request->is('users/authorizations/*/edit') || $request->is('users/authorizations/*/update')) {
          if (!Auth::user()->hasPermissionTo('Edit User Authorizations')) {
              abort('403');
          } else {
              return $next($request);
          }
        }

        return $next($request);
    }
}
