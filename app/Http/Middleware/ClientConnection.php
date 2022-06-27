<?php

namespace App\Http\Middleware;

use Closure;

use Ping;
use Request;
use Illuminate\Support\Facades\Auth;

use App\User;

class ClientConnection
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
        $client_ip = Request::ip();
        $health = Ping::check($client_ip);
        if (Auth::check() && $health == 200) {
            return $next($request);
        } else {
            return redirect()->back();
        }
    }
}
