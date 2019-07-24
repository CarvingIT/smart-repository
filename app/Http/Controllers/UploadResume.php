<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadResume extends Controller
{
    //
	function index(Request $request){
                #echo "Upload Resume in DOCX format";
                $success = $request->file('resume')->store('uploaded_resumes');
                if(!empty($success)){
                echo "Uploaded resume successfully!<br />";
                echo "<a href='/upload'>Click here to upload another document</a>";
                }
        }

}
