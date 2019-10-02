<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function loadDocument($document_id){
        $doc = \App\Document::find($document_id);
        return response()->download(storage_path('app/'.$doc->path), null, [], null);
    }
}
