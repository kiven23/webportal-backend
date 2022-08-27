<?php

namespace App\Http\Middleware;

use Closure;

class ValidationPortalClearance
{
    private $routeUrl = "api/validation-portal";
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (
            $request->is("$this->routeUrl/check-auth") || $request->is("$this->routeUrl/validate-template") ||
            $request->is("$this->routeUrl/validate-good-receipt-to-serial") || $request->is("$this->routeUrl/export-to-excel") ||
            $request->is("$this->routeUrl/validate-bp-master-cardcode-ar-invoice")
        ) {
            $user = \Auth::user();
            if ($user->hasRole(['Validation Portal']) && $user->hasPermissionTo("Validate Files")) {
                return $next($request);
            } else {
                abort('403');
            }
        }
    }
}
