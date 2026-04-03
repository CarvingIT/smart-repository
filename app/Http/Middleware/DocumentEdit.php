<?php

namespace App\Http\Middleware;

use Closure;

class DocumentEdit
{
    use EnforcesCollectionTwoFactor;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->user() || !$request->user()->canEditDocument($request->document_id)){
		abort(403, 'Forbidden');
        }

        $document = \App\Document::find($request->document_id);
        if ($document) {
            $twoFactorRedirect = $this->enforceCollectionTwoFactor($request, (int) $document->collection_id);
            if ($twoFactorRedirect) {
                return $twoFactorRedirect;
            }
        }

        return $next($request);
    }
}
