<?php

namespace App\Http\Middleware;

use Closure;

class CollectionView
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
        if(!$request->user()->hasPermission($request->collection_id, 'VIEW')){
            // Use a view for error pages
            return response('Access Denied', 403)->header('Content-Type', 'text/plain');
        }
        return $next($request);
    }
}
