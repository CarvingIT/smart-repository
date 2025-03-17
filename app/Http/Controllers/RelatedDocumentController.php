<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RelatedDocument;
use App\Document;

class RelatedDocumentController extends Controller
{
	public function addRelatedDocument(Request $request){
		$r_d = new RelatedDocument;
		$r_d->document_id = $request->document_id;
		$r_d->related_document_id = $request->related_document_id;
		$r_d->display_order = $request->display_order;
		$r_d->title = $request->title;

		try{
		$r_d->save();
		}
		catch(\Exception $e){
			// do nothing
			echo $e->getMessage();
		}
		$doc = Document::find($r_d->document_id);
		return redirect('/collection/'.$doc->collection->id.'/document/'.$doc->id.'/details');
	}
}
