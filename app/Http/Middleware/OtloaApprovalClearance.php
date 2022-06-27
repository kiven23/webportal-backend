<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\AccessChartUserMap as AccessUser;

class OtloaApprovalClearance
{
    
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Overlook - Overtime Approvals
            if ($request->is('approvals/overtimes/overlook')) {
                if (Auth::user()->hasAnyPermission(['Approve Overtimes',
                                                    'Return Overtimes',
                                                    'Reject Overtimes',
                                                    'Approve Leave of Absences',
                                                    'Return Leave of Absences',
                                                    'Reject Leave of Absences']) &&
                    Auth::user()->hasAnyPermission(['Overlook Overtimes', 'Overlook Leave of Absences'])) {
                    return $next($request);
                } else {
                    return abort('403');
                }
            }

            // Show - Overtime Approvals
            if ($request->is('approvals/overtimes/pending')) {
                if (Auth::user()->hasAnyPermission(['Approve Overtimes',
                                                    'Return Overtimes',
                                                    'Reject Overtimes',
                                                    'Approve Leave of Absences',
                                                    'Return Leave of Absences',
                                                    'Reject Leave of Absences']) &&
                    !Auth::user()->hasAnyPermission(['Overlook Overtimes', 'Overlook Leave of Absences'])) {
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

            // Approve Overtime
            if ($request->is('approvals/overtimes/*/approve') || $request->is('approvals/overtimes/*/proceed_approve')) {
                if (Auth::user()->hasPermissionTo('Approve Overtimes')) {
                    if (Auth::user()->hasPermissionTo('Overlook Overtimes')) {
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

            // Return Overtime
            if ($request->is('approvals/overtimes/*/return') || $request->is('approvals/overtimes/*/proceed_return')) {
                if (Auth::user()->hasPermissionTo('Return Overtimes')) {
                    if (Auth::user()->hasPermissionTo('Overlook Overtimes')) {
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

            // Reject Overtime
            if ($request->is('approvals/overtimes/*/reject') || $request->is('approvals/overtimes/*/proceed_reject')) {
                if (Auth::user()->hasPermissionTo('Reject Overtimes')) {
                    if (Auth::user()->hasPermissionTo('Overlook Overtimes')) {
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
