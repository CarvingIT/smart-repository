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
		
		if(!$collection){
			abort(403, 'Forbidden');
		}
		else if($collection->type != 'Public'){
			if(!$user){
				abort(403, 'Forbidden');
			}
			else if(!$user->hasPermission($request->collection_id, 'VIEW')
				&& !$user->hasPermission($request->collection_id, 'VIEW_OWN')
				&& !$user->hasPermission($request->collection_id, 'MAINTAINER')
				){
				abort(403, 'Forbidden');
			}
		}
        return $next($request);
    }
}
