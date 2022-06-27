<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MessageClearance {

    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Compose Message
            if ($request->is('messages/message_casts') || $request->is('messages/message_casts/send')) {
                if (Auth::user()->hasPermissionTo('Compose Messages')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
        }

        return $next($request);
    }
}
