<?php

namespace App\Http\Middleware;

use Closure;

class ItemMasterData
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
         //ITEM MASTER DATA
         if ($request->is('api/itemmasterdata/oitm/index') || $request->is('api/itemmasterdata/oitm/fields')
       || $request->is('api/itemmasterdata/oitm/progress') || $request->is('api/itemmasterdata/oitm/create') || $request->is('api/itemmasterdata/oitm/update' )
            ) {
            if (\Auth::user()->hasRole(['Item Master Data Admin'])) {
                return $next($request);
            } else {
                abort('403');
            }
         
     }       
    }
}
