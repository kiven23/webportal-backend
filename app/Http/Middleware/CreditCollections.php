<?php

namespace App\Http\Middleware;

use Closure;

class CreditCollections
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
            // Show cdr
            if ($request->is('api/digitized/index')) {
                if (\Auth::user()->hasRole(['CDR Branch',
                                            'CDR Main Office'])) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }
 
            // Delete cdr
            if ($request->is('api/digitized/delete')||$request->is('api/digitized/trash')) {
                if (\Auth::user()->hasPermissionTo('Delete CDR')) {
                    return $next($request);
                } else {
                    abort('403');
                }
                
            }  
    
            // Create cdr
            if ($request->is('api/digitized/upload')) {
                if (\Auth::user()->hasPermissionTo('Create CDR')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            } 
       
            // Edit cdr
            if ($request->is('api/digitized/update')) {
                if (\Auth::user()->hasPermissionTo('Edit CDR')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }    
      
            // Download cdr
            if ($request->is('api/digitized/download')) {
                if (\Auth::user()->hasPermissionTo('Download CDR')) {
                    return $next($request);
                } else {
                    abort('403');
                }
            }   
   
    }
}
