<?php

namespace App\Http\Middleware;

use Closure;

class Maintainer
{
    use EnforcesCollectionTwoFactor;

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
		abort(403, 'Forbidden');
        }

        $twoFactorRedirect = $this->enforceCollectionTwoFactor($request, (int) $request->collection_id);
        if ($twoFactorRedirect) {
            return $twoFactorRedirect;
        }

        return $next($request);
    }
}
