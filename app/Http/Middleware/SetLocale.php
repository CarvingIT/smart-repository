<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $language = 'en';

        if($request->session()->has('sr_lang')){
        $language = $request->session()->get('sr_lang', 'en');
        }
 
        if (isset($language)) {
            app()->setLocale($language);
        }

        return $next($request);
    }
}
