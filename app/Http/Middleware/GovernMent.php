<?php

namespace App\Http\Middleware;

use Closure;

class GovernMent
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
        // Show Agency
         if ($request->is('api/agencies/index')) {
            if (\Auth::user()->hasRole(['Agencies Admin User',
                                        'Agencies Branch User',
                                        'Agencies Guest User'])) {
                return $next($request);
            } else {
                abort('403');
            }
        }
        // Show Archived
        if ($request->is('api/archived/index')) {
            if (\Auth::user()->hasRole(['Archived Admin',
                                        'Archived User'
                                        ])) {
                return $next($request);
            } else {
                abort('403');
            }
        }
        // Delete Agency
        if ($request->is('api/agencies/delete')||$request->is('api/agencies/trash')) {
            if (\Auth::user()->hasPermissionTo('Delete Agencies File')) {
                return $next($request);
            } else {
                abort('403');
            }
            
        }  
        // Delete Archived
        if ($request->is('api/archived/delete')) {
            if (\Auth::user()->hasPermissionTo('Delete Archived')) {
                return $next($request);
            } else {
                abort('403');
            }
            
        }  
        // Create agency
        if ($request->is('api/agencies/store')) {
            if (\Auth::user()->hasPermissionTo('Create Agencies File')) {
                return $next($request);
            } else {
                abort('403');
            }
        } 
        // Create Archived
        if ($request->is('api/archived/store')) {
            if (\Auth::user()->hasPermissionTo('Create Archived')) {
                return $next($request);
            } else {
                abort('403');
            }
        } 
        // Edit Agency
        if ($request->is('api/agencies/update')) {
            if (\Auth::user()->hasPermissionTo('Edit Agencies File')) {
                return $next($request);
            } else {
                abort('403');
            }
        }    
        // Edit Archived
        if ($request->is('api/archived/update')) {
            if (\Auth::user()->hasPermissionTo('Edit Archived')) {
                return $next($request);
            } else {
                abort('403');
            }
        }   
        // Download Agency
        if ($request->is('api/agencies/download')) {
            if (\Auth::user()->hasPermissionTo('Download Agencies Files')) {
                return $next($request);
            } else {
                abort('403');
            }
        }   
        // Download Archived
        if ($request->is('api/archived/download')) {
            if (\Auth::user()->hasPermissionTo('Download Archived Files')) {
                return $next($request);
            } else {
                abort('403');
            }
        }   
       
    }
}
