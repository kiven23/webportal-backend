<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\AccessChartUserMap as AccessUser;

class PoFileApprovalClearance
{
    
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            // Overlook - PO File Approvals
            if ($request->is('approvals/purchase-orders/files/overlook')) {
                if (Auth::user()->hasAnyPermission(['Approve Purchase Order Files',
                                                    'Reject Purchase Order Files']) &&
                    Auth::user()->hasAnyPermission(['Overlook Purchase Order Files'])) {
                    return $next($request);
                } else {
                    return abort('403');
                }
            }

            // Show - PO File Approvals
            if ($request->is('approvals/purchase-orders/files/pending')) {
                if (Auth::user()->hasAnyPermission(['Purchase Order File Lists',
                                                    'Approve Purchase Order Files',
                                                    'Reject Purchase Order Files']) &&
                    !Auth::user()->hasAnyPermission(['Overlook Purchase Order Files'])) {
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

            // Approve PO File
            if ($request->is('approvals/*/purchase-orders/files/approve') || $request->is('approvals/*/purchase-orders/files/proceed_approve')) {
                if (Auth::user()->hasPermissionTo('Approve Purchase Order Files')) {
                    if (Auth::user()->hasPermissionTo('Overlook Purchase Order Files')) {
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

            // Reject PO File
            if ($request->is('approvals/*/purchase-orders/files/reject') || $request->is('approvals/*/purchase-orders/files/proceed_reject')) {
                if (Auth::user()->hasPermissionTo('Reject Purchase Order Files')) {
                    if (Auth::user()->hasPermissionTo('Overlook Purchase Order Files')) {
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