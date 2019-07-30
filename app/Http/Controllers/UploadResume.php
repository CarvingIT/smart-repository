<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\File;

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
			$filepath = $request->file('resume')->storeAs('uploaded_resumes',$filename);
			$filesize = $request->file('resume')->getClientSize();
			$mimetype = $request->file('resume')->getMimeType();
			$success = $request->file('resume')->storeAs('uploaded_resumes',$filename);
       		        #$success = $request->file('resume')->store('uploaded_resumes');

			$file = new File;
			$file->name = $filename;
			$file->size = $filesize;
			$file->mime = $mimetype;
			$data = file_get_contents($request->file('resume')->getRealPath());
			$file->content = $data;
			echo $data;
			$file->save();
		}
                if(!empty($success)){
                	echo "Uploaded resume successfully!<br />";
                	echo "<a href='/upload'>Click here to upload another document</a>";
                }
        }

}
