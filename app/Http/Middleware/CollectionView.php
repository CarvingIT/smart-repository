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
		$user = \Auth::user();
        $collection = \App\Collection::find($request->collection_id);

        if(!$collection || ($collection->type != 'Public' && 
			(!$user || !$request->user()->hasPermission($request->collection_id, 'VIEW')))){
			abort(403, 'Forbidden');
        }
        return $next($request);
    }
}
