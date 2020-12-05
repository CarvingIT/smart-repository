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
        $collection = \App\Collection::find($request->collection_id);
        if($collection->type != 'Public' && !$request->user()->hasPermission($request->collection_id, 'VIEW')){
		abort(403);
        }
        return $next($request);
    }
}
