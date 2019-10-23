<?php

namespace App\Http\Middleware;

use Closure;

class DBSelect
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
        // find the subdomain
        $host = $request->getHost();
        $domain = env('APP_DOMAIN');
        $subdomain = str_replace('.'.$domain, '', $host);
        $org = \App\Organization::where('subdomain','=',$subdomain)->first();
        if(!empty($org->db)){
            config([
                'database.connections.mysql.database'=> $org->db,
            ]);
            \DB::reconnect('mysql');
            return $next($request);
        }
        else{
            return response('Not found', 404)->header('Content-Type', 'text/plain');
        }
    }
}
