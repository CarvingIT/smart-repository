<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;
use App\Classes\DocxToTextConversion;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function loadDocument($document_id){
        $doc = \App\Document::find($document_id);
        $open_in_browser_types = array('application/pdf', 'text/plain');
        if(in_array($doc->type, $open_in_browser_types)){
            return response()->download(storage_path('app/'.$doc->path), null, [], null);
        }
        return response()->download(storage_path('app/'.$doc->path));
    }

	public function showUploadForm($collection_id)
    {
            $collection = \App\Collection::find($collection_id);
       		return view('upload', ['collection'=>$collection]);
   	}

	public function showEditForm($document_id)
    {
            $document = \App\Document::find($document_id);
            $collection = \App\Collection::find($document->collection_id);
       		return view('upload', ['collection'=>$collection, 'document'=>$document]);
   	}

	public function upload(Request $request){
        /*  !!!!
            More work needed here
        */
		if($request->hasFile('document')){
			$filename = $request->file('document')->getClientOriginalName();
            $new_filename = \Auth::user()->id.'_'.time().'_'.$filename;
			$filepath = $request->file('document')->storeAs('smartarchive_assets/'.$request->input('collection_id').'/'.\Auth::user()->id,$new_filename);
			$filesize = $request->file('document')->getClientSize();
			$mimetype = $request->file('document')->getMimeType();
            if(!empty($request->input('document_id'))){
                $d = Document::find($request->input('document_id'));
            }
            else{
			    $d = new Document;
            }

            if(!empty($request->input('title'))){
                $d->title = $request->input('title');
            }
            else{
                $d->title = $this->autoDocumentTitle($request->file('document')->getClientOriginalName());
            }
            $d->collection_id = $request->input('collection_id');
            $d->created_by = \Auth::user()->id;
			$d->size = $filesize;
			$d->type = $mimetype;
            $d->path = $filepath;
			$d->text_content = $this->extractText($d);
			//$d->text_content = 'Not available';
			$d->save();

            $this->createDocumentRevision($d);
		}
           return redirect('/collection/'.$request->input('collection_id')); 
    }

    public function createDocumentRevision($d){
        $revision = new \App\DocumentRevision; 
        $revision->document_id = $d->id;
        $revision->created_by = $d->created_by;
        $revision->path = $d->path;
        $revision->type = $d->type;
        $revision->size = $d->size;
        $revision->save();        
    }

    public function autoDocumentTitle($filename){
        $filename_chunks = explode(".",$filename);
        $title = $filename_chunks[0];
        $title = str_replace('_',' ',$title);
        $title = str_replace('-',' ',$title);
        $title = ucfirst($title);
        return $title;
    }

    public function deleteDocument($document_id){
        $d = \App\Document::find($document_id);
        $collection_id = $d->collection_id;
        $d->delete();
        return redirect('/collection/'.$collection_id); 
    }

	public function documentRevisions($document_id)
    {
            $document_revisions = \App\DocumentRevision::where('document_id','=', $document_id)
                ->orderBy('id','DESC')->get();
       		return view('document-revisions', ['document_revisions'=>$document_revisions]);
   	}

    public function loadRevision($revision_id){
        $doc = \App\DocumentRevision::find($revision_id);
        $open_in_browser_types = array('application/pdf', 'text/plain');
        if(in_array($doc->type, $open_in_browser_types)){
            return response()->download(storage_path('app/'.$doc->path), null, [], null);
        }
        return response()->download(storage_path('app/'.$doc->path));
    }

    public function extractText($d){
        if($d->type == 'application/pdf'){
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile(storage_path('app/'.$d->path));
            $text = $pdf->getText();
            $text = str_replace(array('&', '%', '$', "\n"), ' ', $text);
            return $text;
        }
        else if(preg_match('/^text\//', $d->type)){
            return file_get_contents(storage_path('app/'.$d->path));
        }
        else{
	        $doc = new \App\DocXtract(storage_path('app/'.$d->path));
		    return $doc->convertToText();
        }
    }
}
