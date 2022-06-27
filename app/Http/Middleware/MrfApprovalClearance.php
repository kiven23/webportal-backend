<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\AccessChartUserMap as AccessUser;

class MrfApprovalClearance
{
    
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Overlook - MRF Approvals
            if ($request->is('approvals/maint-requests/overlook')) {
                if (Auth::user()->hasAnyPermission(['Maintenance Request Lists',
                                                    'Approve Maintenance Requests',
                                                    'Cancel Maintenance Requests']) &&
                    Auth::user()->hasAnyPermission(['Overlook Maintenance Requests'])) {
                    return $next($request);
                } else {
                    return abort('403');
                }
            }

            // Show - MRF Approvals
            if ($request->is('approvals/maint-requests/pending')) {
                if (Auth::user()->hasAnyPermission(['Maintenance Request Lists',
                                                    'Approve Maintenance Requests',
                                                    'Cancel Maintenance Requests']) &&
                    !Auth::user()->hasAnyPermission(['Overlook Maintenance Requests'])) {
                    // Check user if approver base on access chart
                    $access_chart = AccessUser::where('user_id', Auth::user()->id)->first();
                    if (!$access_chart) {
                        return abort('403');
                    } else {
                        return $next($request);
                    }
                } else {
                    return abort('403');
                }
            }

            // Approve MRF
            if ($request->is('approvals/*/maint-requests/approve') || $request->is('approvals/*/maint-requests/proceed_approve')) {
                if (Auth::user()->hasPermissionTo('Approve Maintenance Requests')) {
                    if (Auth::user()->hasPermissionTo('Overlook Maintenance Requests')) {
                        return $next($request);
                    } else {
                        // Check if user is approver base on access chart
                        $access_chart = AccessUser::where('user_id', Auth::user()->id)->first();
                        if (!$access_chart) {
                            return abort('403');
                        } else {
                            return $next($request);
                        }
                    }
                } else {
                    return abort('403');
                }
            }

            // Cancel MRF
            if ($request->is('approvals/*/maint-requests/cancel') || $request->is('approvals/*/maint-requests/proceed_cancel')) {
                if (Auth::user()->hasPermissionTo('Cancel Maintenance Requests')) {
                    if (Auth::user()->hasPermissionTo('Overlook Maintenance Requests')) {
                        return $next($request);
                    } else {
                        // Check user if approver base on access chart
                        $access_chart = AccessUser::where('user_id', Auth::user()->id)->first();
                        if (!$access_chart) {
                            return abort('403');
                        } else {
                            return $next($request);
                        }
                    }
                } else {
                    return abort('403');
                }
            }
        }

        return $next($request);
    }
}
