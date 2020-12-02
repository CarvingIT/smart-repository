<?php

namespace App\Http\Middleware;

use Closure;

class AppExpiry
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
	$key = base64_decode(env('APP_CYPHER'));
	$date = new \DateTime($key);
	$now = $now = new \DateTime();
       	if($date < $now){
       		return response('Access Denied', 403)->header('Content-Type', 'text/plain');
       	}
        return $next($request);
    }
}
