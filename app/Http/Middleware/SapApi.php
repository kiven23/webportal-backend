<?php

namespace App\Http\Middleware;

use Closure;

class SapApi
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

                // SHOW CREDIT STANDING
                        
                    if ($request->is('api/public/credit/standing/index')) {
                        if (\Auth::user()->hasRole(['Credit Standing Access'])) {
                            return $next($request);
                        } else {
                            abort('403');
                        }
                    }

                // GENERATE CREDIT STANDING REPORTS
                    if ($request->is('api/public/credit/standing/generate')) {
                        if (\Auth::user()->hasPermissionTo('Generate Credit Standing')) {
                            return $next($request);
                        } else {
                            abort('403');
                        }
                        
                    }  

  
                // SHOW INSTALLMENT DUE
                if ($request->is('api/public/index')) {
                    if (\Auth::user()->hasRole(['Installment Due Access'])) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
                // GET INSTALLMENT 
                if ($request->is('api/public/installment')) {
                    if (\Auth::user()->hasPermissionTo('Get Installment Due')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                    
                }  

                // SHOW AGING RECONCILIATION
                if ($request->is('api/public/installment/index')) {
                    if (\Auth::user()->hasRole(['Aging Recon Access'])) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }
                // CREATE AND UPDATE RECON
                if ($request->is('api/public/installment/create')) {
                    if (\Auth::user()->hasPermissionTo('Create Manual Aging')) {
                        return $next($request);
                    } else {
                        abort('403');
                    } 
                }
                if ($request->is('api/public/installment/updatemanual')) {
                    if (\Auth::user()->hasPermissionTo('Update Manual Aging')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                    
                }    
                 
                //BRANCH SEGMENT 
                if ($request->is('api/public/branch/segment') || $request->is('api/public/calculate/grade')) {
                    if (\Auth::user()->hasPermissionTo('SapApiAccess Branch')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                    
                }
                //BRANCH SAP ACCESS CRB
                if ($request->is('api/public/reports/incoming/crb') || 
                    $request->is('api/public/reports/queries/searchofvehicleparts') || 
                    $request->is('api/public/reports/queries/invoicequeryseriesrevised') ||
                    $request->is('api/public/reports/queries/marketingarinvoicequery') || 
                    $request->is('api/public/reports/queries/summaryofcustomerdepositapplied') ||
                    $request->is('api/public/reports/queries/adjustmentsalesdiscount') ||
                    $request->is('api/public/reports/queries/recomputedaccount')||
                    $request->is('api/public/reports/queries/arinvoiceopenbalance') ||
                    $request->is('api/public/reports/queries/incomingpaymentcustomerdeposit') ||
                    $request->is('api/public/reports/queries/incomingpaymentopenbalance') ) {
                    if (\Auth::user()->hasPermissionTo('SapApiAccess Branch')) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                    
                } 
                 
                    


                //SHOW DUNNING LETTERS
                if($request->is('api/credit-dunning/index')) {
                    $user = \Auth::user();
                    if ($user->hasRole(['Dunning Letter Branch', 'Dunning Letter Admin']) && $user->hasPermissionTo("Show Dunning Letters")) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                }

                //DOWNLOAD DUNNING LETTERS 
                if($request->is('api/credit-dunning/download-letters')) {
                    if (\Auth::user()->hasPermissionTo("Download Dunning Letters")) {
                        return $next($request);
                    } else {
                        abort('403');
                    }
                } 


    }
}
