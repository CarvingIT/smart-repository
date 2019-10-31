<?php

namespace App\Http\Middleware;

use Closure;

class Maintainer
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
        if(!$request->user() || !$request->user()->hasPermission($request->collection_id, 'MAINTAINER')){
            return response('Access Denied', 403)->header('Content-Type', 'text/plain');
        }
        return $next($request);
    }
}
