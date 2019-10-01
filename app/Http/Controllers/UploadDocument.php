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
                #echo "Upload Resume in DOCX format";
        /*  !!!!
            More work needed here
            The location of the uploaded documents (asset-store) should be configurable and must come from .env file.
            Each collection may have one directory named after the ID of the collection.
        */
		if($request->hasFile('resume')){
			$filename = $request->file('resume')->getClientOriginalName();
			$filepath = $request->file('resume')->storeAs('public/uploaded_resumes',$filename);
			$filesize = $request->file('resume')->getClientSize();
			$mimetype = $request->file('resume')->getMimeType();
			$success = $request->file('resume')->storeAs('public/uploaded_resumes',$filename);
			echo $filepath;
			$converter = new DocxToTextConversion('/public/uploaded_resumes/'.$filename);
			$data = $converter->convertToText();
			echo $data;
exit;

			$file = new File;
			$file->name = $filename;
			$file->size = $filesize;
			$file->mime = $mimetype;
			$file->content = $data;
			$file->save();
		}
                if(!empty($success)){
                	echo "Uploaded resume successfully!<br />";
                	echo "<a href='/upload'>Click here to upload another document</a>";
                }
        }

}
