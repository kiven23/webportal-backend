<?php

namespace App\Http\Middleware;

use Closure;

class RevolvingFundsClearance
{
    private $routeUrl = "api/revolving-fund";
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeUrl = $this->routeUrl;
        $checkVerificationUrl = "$routeUrl/check-voucher-verification";
        $checkForTransUrl = "$routeUrl/check-voucher-for-transmittal";
        $expensesForChkPrepUrl = "$routeUrl/expenses-for-check-preparation";
        $availRvFundOnHandUrl = "$routeUrl/avail-rv-fund-on-hand";
        $PreparingUrlHistory = "$routeUrl/preparation";
        $CKUrlHistory = "$routeUrl/revolving-fund";
        if ($request->is("$routeUrl/index")) {
            $authUser = \Auth::user();
            if ($authUser->hasPermissionTo("Show Revolving Funds") || $authUser->hasPermissionTo("Show All Revolving Funds")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        // if ($request->is("$routeUrl/create")) {
        //     if (\Auth::user()->hasPermissionTo("Add Revolving Funds")) {
        //         return $next($request);
        //     } else {
        //         abort('403');
        //     }
        // }

        if ($request->is("$routeUrl/view/*")) {
            if (\Auth::user()->hasPermissionTo("View Revolving Funds")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$routeUrl/update/*/cash-advances")) {
            if (\Auth::user()->hasPermissionTo("Edit Revolving Fund Cash Advances")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        // if ($request->is("$routeUrl/update/*")) {
        //     if (\Auth::user()->hasPermissionTo("Edit Revolving Funds")) {
        //         return $next($request);
        //     } else {
        //         abort('403');
        //     }
        // }

        // if ($request->is("$routeUrl/delete/items")) {
        //     if (\Auth::user()->hasPermissionTo("Delete Revolving Funds")) {
        //         return $next($request);
        //     } else {
        //         abort('403');
        //     }
        // }

        if ($request->is("$routeUrl/print/*")) {
            if (\Auth::user()->hasPermissionTo("Print Revolving Funds")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$routeUrl/update-avail-rf-on-hand/*")) {
            if (\Auth::user()->hasPermissionTo("Update Available Revolving Fund On Hand")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$checkVerificationUrl/create") || $request->is("$checkForTransUrl/create") || $request->is("$expensesForChkPrepUrl/create")) {
            if (\Auth::user()->hasPermissionTo("Add Revolving Fund Expenses")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$checkVerificationUrl/update/*") || $request->is("$checkForTransUrl/update/*") || $request->is("$expensesForChkPrepUrl/update/*")) {
            if (\Auth::user()->hasPermissionTo("Edit Revolving Fund Expenses")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$checkVerificationUrl/destroy/*") || $request->is("$checkForTransUrl/destroy/*") || $request->is("$expensesForChkPrepUrl/destroy/*")) {
            if (\Auth::user()->hasPermissionTo("Delete Revolving Fund Expenses")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$checkVerificationUrl/update-status/*")) {
            if (\Auth::user()->hasPermissionTo("Edit Revolving Fund Expenses Status")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$checkForTransUrl/transmit")) {
            if (\Auth::user()->hasPermissionTo("Transmit Revolving Fund Expenses")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$expensesForChkPrepUrl/replenish")) {
            if (\Auth::user()->hasPermissionTo("Replenish Revolving Fund Expenses")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$availRvFundOnHandUrl/index")) {
            if (\Auth::user()->hasPermissionTo("Show Available Revolving Fund On Hand")) {
                return $next($request);
            } else {
                abort('403');
            }
        }
        if ($request->is("$PreparingUrlHistory/history")) {
            if (\Auth::user()->hasPermissionTo("View Revolving Funds")) {
                return $next($request);
            } else {
                abort('403');
            }
        }
        if ($request->is("api/revolving-fund/ck/history")) {
            if (\Auth::user()->hasPermissionTo("View Revolving Funds")) {
                return $next($request);
            } else {
                abort('403');
            }
        }
        if ($request->is("$PreparingUrlHistory/history/print")) {
            if (\Auth::user()->hasPermissionTo("View Revolving Funds")) {
                return $next($request);
            } else {
                abort('403');
            }
        }
        if ($request->is("$availRvFundOnHandUrl/history")) {
            if (\Auth::user()->hasPermissionTo("View Revolving Funds")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$availRvFundOnHandUrl/updateOrCreate")) {
            $authUser = \Auth::user();
            if ($authUser->hasPermissionTo("Add Revolving Funds") || $authUser->hasPermissionTo("Edit Revolving Funds")) {
                return $next($request);
            } else {
                abort('403');
            }
        }

        if ($request->is("$availRvFundOnHandUrl/print")) {
            if (\Auth::user()->hasPermissionTo("Print Available Revolving Fund On Hand")) {
                return $next($request);
            } else {
                abort('403');
            }
        }
    }
}
