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
        $db = $org->db;
        config([
                'database.connections.mysql.database'=> $db,
            ]);
        \DB::reconnect('mysql');
        return $next($request);
    }
}
