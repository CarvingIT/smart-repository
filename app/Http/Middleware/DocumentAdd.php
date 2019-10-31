<?php

namespace App\Http\Middleware;

use Closure;

class DocumentAdd
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
        if(
            !$request->user() || 
            (
            !$request->user()->hasPermission($request->collection_id, 'CREATE') &&
            !$request->user()->hasPermission($request->collection_id, 'EDIT_ANY') &&
            !$request->user()->hasPermission($request->collection_id, 'EDIT_OWN') 
            )
        ){
            // Use a view for error pages
            return response('Access Denied', 403)->header('Content-Type', 'text/plain');
        }
        return $next($request);
    }
}
