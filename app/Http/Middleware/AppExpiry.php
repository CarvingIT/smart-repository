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
		$maint_info = array('time'=>time(), 
			'message'=>'', 
			'retry'=>null, 
			'allowed'=>array());
		$maint_info_string = json_encode($maint_info);
		file_put_contents(storage_path().'/framework/down', $maint_info_string);
       	}
        return $next($request);
    }
}
