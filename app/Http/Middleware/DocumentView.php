<?php

namespace App\Http\Middleware;

use Closure;

class DocumentView
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
        $document = \App\Document::find($request->document_id);
        $collection = \App\Collection::find($document->collection_id);
        if($collection->type != 'Public' && 
            !($request->user() && $request->user()->hasPermission($collection->id, 'VIEW'))){
            // Use a view for error pages
            return response('Access Denied', 403)->header('Content-Type', 'text/plain');
        }
        return $next($request);
    }
}
