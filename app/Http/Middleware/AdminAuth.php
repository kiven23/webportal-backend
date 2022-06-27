<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class AdminAuth {

    public function handle($request, Closure $next) {
        if (\Auth::check()) {
            $user = User::all()->count();
            if (!($user == 1)) {
                if (!Auth::user()->hasPermissionTo('Administer roles & permissions')) { //If user does not have this permission
                    abort('403');
                }
            }
        }

        // Prevent deletion of Super Admin
        if ($request->is('roles/1/trash') || $request->is('roles/1/delete')) {
            abort('403');
        }

        // Prevent deletion of Administer roles & permissions
        if ($request->is('permissions/1/trash') || $request->is('permissions/1/delete')) {
            abort('403');
        }

        return $next($request);
    }
}
