<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
	
	private $unwantedHeaderList = [
        'X-Powered-By',
        'Server',
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Clear-Site-Data', "'cache', 'cookies', 'storage', 'executionContents'");
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Content-Security-Policy', "script-src 'self' 'unsafe-inline' code.jquery.com mozilla.github.io; style-src 'self' 'unsafe-inline' fonts.googleapis.com code.jquery.com"); 
		foreach($this->unwantedHeaderList as $h){
			$response->headers->remove($h);
		}
        return $response;
    }
}
