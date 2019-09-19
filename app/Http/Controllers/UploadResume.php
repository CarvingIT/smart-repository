<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\File;
use App\Classes\DocxToTextConversion;



use Illuminate\Support\Facades\Storage;
#use Illuminate\Support\Facades\Input;
#use Illuminate\Support\Facades\DB;

class UploadResume extends Controller
{
    //
	public function index()
    	{
       		return view('upload');
   	}

	public function uploadResume(Request $request){
                #echo "Upload Resume in DOCX format";
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
