<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\DatabaseConnection;

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
        $db_connection_details = array('host' => env('DB_HOST'), 
            'username'=> 'smartarchive',
            'password'=> 'smartarchive123', 
            'database'=>$org->db);
        //DatabaseConnection::setConnection($db_connection_details);
        echo $org->db;
        config(['database.connections.onthefly' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'username' => 'smartarchive',
            'password' => 'smartarchive123',
            'database'=>$org->db
        ]]);

        return $next($request);
    }
}
