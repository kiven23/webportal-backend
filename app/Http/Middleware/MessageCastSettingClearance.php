<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MessageCastSettingClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
          // Message Cast Settings
          if ($request->is('settings/message_casts') || $request->is('settings/message_casts/update')) {
              if (!Auth::user()->hasPermissionTo('Edit Message Cast Settings')) {
                  abort('403');
              } else {
                  return $next($request);
              }
          }
        }

        return $next($request);
    }
}
