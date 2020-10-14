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
        $document = \App\Document::find($request->route('document_id'));
	if($document->collection->content_type == 'Uploaded documents'){ // restriction only for Uploaded documents
        $document = \App\Document::find($request->document_id);
        if($document->collection->type != 'Public' && 
            !($request->user() && $request->user()->hasPermission($document->collection->id, 'VIEW'))){
            // Use a view for error pages
            return response('Access Denied', 403)->header('Content-Type', 'text/plain');
        }
	}
        return $next($request);
    }
}
