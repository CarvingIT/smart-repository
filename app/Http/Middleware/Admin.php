<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        if(!$request->user()->hasRole('admin')){
            // Needs more work. Redirect user to a page that shows permission-denied message
            return redirect('/home'); 
        }
        return $next($request);
    }
}
