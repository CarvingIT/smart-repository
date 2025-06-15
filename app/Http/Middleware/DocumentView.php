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
	if($document && $document->collection->content_type == 'Uploaded documents'){ // restriction only for Uploaded documents
        $document = \App\Document::find($request->document_id);
		$user = $request->user();

        if($document->collection->type != 'Public' 
            && !($user && $user->hasPermission($document->collection->id, 'VIEW'))
			&& $document->created_by != $user->id
		){
	    abort(403, 'Forbidden');
        }
	}
        return $next($request);
    }
}
