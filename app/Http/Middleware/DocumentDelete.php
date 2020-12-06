<?php

namespace App\Http\Middleware;

use Closure;

class DocumentDelete
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
        if(!$request->user() || !$request->user()->canDeleteDocument($request->document_id)){
		abort(403, 'Forbidden');
        }
        return $next($request);
    }
}
