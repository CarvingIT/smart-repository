<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Document;
use App\Classes\DocxToTextConversion;
use Illuminate\Support\Facades\Storage;
#use Illuminate\Support\Facades\Input;
#use Illuminate\Support\Facades\DB;

class UploadDocument extends Controller
{
    //
	public function showForm($collection_id)
    {
            $collection = \App\Collection::find($collection_id);
       		return view('upload', ['collection'=>$collection]);
   	}

	public function upload(Request $request){
        /*  !!!!
            More work needed here
        */
		if($request->hasFile('document')){
			$filename = $request->file('document')->getClientOriginalName();
            $new_filename = \Auth::user()->id.'_'.time().'_'.$filename;
			$filepath = $request->file('document')->storeAs('smartarchive_assets/'.$request->input('collection_id'),$new_filename);
			$filesize = $request->file('document')->getClientSize();
			$mimetype = $request->file('document')->getMimeType();
			$d = new Document;
            /*
			$converter = new DocxToTextConversion('/public/uploaded_resumes/'.$filename);
			$text_content = $converter->convertToText();
			$d->text_content = $text_content;
            */
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
			$d->text_content = 'Not available';
			$d->save();
		}
           return redirect('/collection/'.$request->input('collection_id')); 
    }

    public function autoDocumentTitle($filename){
        $filename_chunks = explode(".",$filename);
        $title = $filename_chunks[0];
        $title = str_replace('_',' ',$title);
        $title = str_replace('-',' ',$title);
        $title = ucfirst($title);
        return $title;
    }

}
