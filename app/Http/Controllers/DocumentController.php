<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Session;

class DocumentController extends Controller
{
    public function loadDocument($document_id){
        $doc = \App\Document::find($document_id);
        $ext = pathinfo($doc->path, PATHINFO_EXTENSION);
        $open_in_browser_types = explode(',',env('FILE_EXTENSIONS_TO_OPEN_IN_BROWSER'));
        $this->recordHit($document_id);
        if(in_array($ext, $open_in_browser_types)){
            return response()->download(storage_path('app/'.$doc->path), null, [], null);
        }
        return response()->download(storage_path('app/'.$doc->path));
    }
    
    public function recordHit($document_id){
        $hit = new \App\DocumentHit;
        $revision = \App\DocumentRevision::where('document_id','=', $document_id)
            ->orderby('id', 'DESC')->first();
        $hit->document_id = $document_id;
        $hit->revision_id = $revision->id;
        $hit->user_id = empty(\Auth::user()->id)? null : \Auth::user()->id;
        $hit->save(); 
    }

	public function showUploadForm($collection_id)
    {
            $size_limit = ini_get("upload_max_filesize");	
            $collection = \App\Collection::find($collection_id);
            $document = new \App\Document;
       		return view('upload', ['collection'=>$collection, 'document'=>$document,'activePage'=>'Upload Document Form','titlePage'=>'Upload Document','size_limit'=>$size_limit]);
   	}

	public function showEditForm($document_id)
    	{
            $size_limit = ini_get("upload_max_filesize");	
            $document = \App\Document::find($document_id);
            $collection = \App\Collection::find($document->collection_id);
	    $has_approval=array();
	    $has_approval = \App\Collection::where('id','=',$document->collection_id)->where('require_approval','=','1')->get();

       		return view('upload', ['collection'=>$collection, 
			'document'=>$document,
			'collection_has_approval'=>$has_approval,
			'activePage'=>'Document Edit Form',
			'titlePage'=>'Document Edit Form',
			'size_limit'=>$size_limit]);
   	}


	public function upload(Request $request){
        /*  !!!!
            More work needed here
        */

############### Filesize validation code starts
	$size_limit = ini_get("upload_max_filesize");
	$actual_size = $this->return_bytes($size_limit); ## Newly added line
	$collection_id = $request->input('collection_id');
        $validator = Validator::make($request->all(), [
	    'document' => 'file|max:'.$actual_size
        ]);
	if ($validator->fails()) {
            Session::flash('alert-danger', 'File size has been exceeded. The file size should not be more than '.$size_limit.'B.');
            return redirect('/collection/'.$collection_id.'/upload');
        }
############### Filesize validation code ends

        if(!empty($request->input('document_id'))){
            $d = Document::find($request->input('document_id'));
        }
        else{
            $d = new Document;
        }
	if($request->hasFile('document')){
		$filename = $request->file('document')->getClientOriginalName();
            	$new_filename = \Auth::user()->id.'_'.time().'_'.$filename;
		$filepath = $request->file('document')->storeAs('smartarchive_assets/'.$request->input('collection_id').'/'.\Auth::user()->id,$new_filename);
		$filesize = $request->file('document')->getClientSize();
		$mimetype = $request->file('document')->getMimeType();

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

            	try{
			    $d->text_content = utf8_encode($this->extractText($d));
            	}
            	catch(\Exception $e){
               		\Log::error($e->getMessage());
                	$d->text_content = '';
            	}
			//$d->save();
            try{
                $d->save();
                Session::flash('alert-success', 'Document uploaded successfully!');
            }
            catch(\Exception $e){
                Session::flash('alert-danger', $e->getMessage());
                return redirect('/collection/'.$request->input('collection_id')); 
            }

            // create revision
            $this->createDocumentRevision($d);
	}
            	if(!empty($request->input('approved_on'))){
            	$d->approved_by = \Auth::user()->id;
		$d->approved_on = now();
            	}
            // extract meta
            $meta = $this->getMetaDataFromRequest($request);
            // put all meta values in a string
            $meta_string = '';
            foreach($meta as $m){
                $meta_string .= ' '.$m['field_value'].' ';
            }
            // save meta data
            $this->saveMetaData($d->id, $meta);
            // also update the text_content of the document
            $d->text_content = $d->text_content . $meta_string;

            try{
                $d->save();
                Session::flash('alert-success', 'Document uploaded successfully!');
            }
            catch(\Exception $e){
                Session::flash('alert-danger', $e->getMessage());
            }
           return redirect('/collection/'.$request->input('collection_id')); 
    }

    public static function importFile($collection_id, $path, $meta=[]){
        // get filename and create a new one
        $path_dirs = explode("/", $path);
        $filename = array_pop($path_dirs);
        $new_filename = '0_'.time().'_'.$filename;

		copy($path, base_path().'/storage/app/smartarchive_assets/'.$collection_id.'/0/'.$new_filename);
		$filesize = filesize($path);
		$mimetype = mime_content_type($path); 
        $d = new Document;
        //echo "$filesize $mimetype\n";
        $dc = new \App\Http\Controllers\DocumentController;
        $d->title = $dc->autoDocumentTitle($filename);
        $d->collection_id = $collection_id;
        $d->created_by = 1;
		$d->size = $filesize;
		$d->type = $mimetype;
        $d->path = 'smartarchive_assets/'.$collection_id.'/0/'.$new_filename;
        try{
            $d->text_content = utf8_encode($dc->extractText($d));
        }
        catch(\Exception $e){
            echo $e->getMessage();
            $d->text_content = '';
        }
            $d->save();
            $dc->createDocumentRevision($d);
    }

    public function createDocumentRevision($d){
        $revision = new \App\DocumentRevision; 
        $revision->document_id = $d->id;
        $revision->created_by = $d->created_by;
        $revision->path = $d->path;
        $revision->type = $d->type;
        $revision->size = $d->size;
        $revision->text_content = $d->text_content;
            try{
                $revision->save();
                Session::flash('alert-success', 'Document uploaded successfully!');
            }
            catch(\Exception $e){
                Session::flash('alert-danger', 'Error: '.$e->getMessage());
            }
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
        $c = \App\Document::find($document_id);
        $collection_id = $c->collection_id;
            $document_revisions = \App\DocumentRevision::where('document_id','=', $document_id)
                ->orderBy('id','DESC')->get();
       		return view('document-revisions', ['document_revisions'=>$document_revisions,'collection_id'=>$collection_id,'title'=>'Smart Repository','activePage'=>'Document Revsions','titlePage'=>'Document Revisions']);
   	}

    public function loadRevision($revision_id){
        $doc = \App\DocumentRevision::find($revision_id);
        $open_in_browser_types = explode(',', env('FILE_EXTENSIONS_TO_OPEN_IN_BROWSER'));
        $ext = pathinfo($doc->path, PATHINFO_EXTENSION);
        if(in_array($ext, $open_in_browser_types)){
            return response()->download(storage_path('app/'.$doc->path), null, [], null);
        }
        return response()->download(storage_path('app/'.$doc->path));
    }

    public function extractText($d){
        $text = '';
        if($d->type == 'application/pdf'){
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile(storage_path('app/'.$d->path));
            $text = $pdf->getText();
            $text = str_replace(array('&', '%', '$', "\n"), ' ', $text);
        }
        else if(preg_match('/^image\//', $d->type)){
            // try OCR
            $text = utf8_encode((new TesseractOCR(storage_path('app/'.$d->path)))->run());
        }
        else if(preg_match('/^text\//', $d->type)){
            $text = file_get_contents(storage_path('app/'.$d->path));
        }
        else{ // for doc, docx, ppt, pptx, xls, xlsx
	        $doc = new \App\DocXtract(storage_path('app/'.$d->path));
		    $text = $doc->convertToText();
        }
        return $text;
    }

    public function getMetaDataFromRequest(Request $request){
        $inputs = $request->all();
        $meta_data = array();
        foreach($inputs as $k=>$v){
            if(preg_match('/^meta_field_/', $k)){
                $field_id = str_replace('meta_field_','', $k);
                array_push($meta_data, array('field_id'=>$field_id, 'field_value'=>$v));
            }
        }
        return $meta_data;
    }

    public function saveMetaData($document_id, $meta_data){
        // first delete old and then save new 
        \App\MetaFieldValue::where('document_id','=', $document_id)->delete();

        foreach($meta_data as $m){
            if(empty($m['field_value'])) continue;
            $m_f = new \App\MetaFieldValue;
            $m_f->document_id = $document_id;
            $m_f->meta_field_id = $m['field_id'];
            $m_f->value = $m['field_value'];
            $m_f->save();
        }
    }

    public function showDetails($document_id){
        $d = Document::find($document_id);
        return view('document-details', ['document'=>$d]);
    }

    public function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = substr($val, 0, -1);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    $val = $this->isa_convert_bytes_to_specified($val,'K',0);
    return $val;
    }

    public function isa_convert_bytes_to_specified($bytes, $to, $decimal_places = 1) {
    $formulas = array(
        'K' => number_format($bytes / 1024, $decimal_places,'',''),
        'M' => number_format($bytes / 1048576, $decimal_places,'',''),
        'G' => number_format($bytes / 1073741824, $decimal_places,'','')
    );
    return isset($formulas[$to]) ? $formulas[$to] : 0;
}

}
