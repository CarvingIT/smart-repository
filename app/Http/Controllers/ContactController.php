<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactFormSubmission;
use App\Mail\EOISubmission;
use Illuminate\Support\Facades\Mail;
use App\Models\Feedback;
use App\Models\EOISubmission as EOISubmissionModel;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
	public function contact(Request $req){
       $req->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
			'address'=>['required', 'string','max:255'],
			'subject'=>['required', 'string','max:255'],
			'message'=>['required', 'string','max:255'],
            'CaptchaCode'=> 'required|in:'.session('captcha_code'),
        ]);

		$email_subject = "ISA RRR :: ".$req->subject;

		//code to send email
		Mail::to(env('WEBMASTER_EMAIL'))->send(new ContactFormSubmission($req));
	
		// add to database
		$feedback = new Feedback;
		$feedback->name = $req->name;
		$feedback->email = $req->email;
		$feedback->address = $req->address;
		$feedback->subject = $req->subject;
		$feedback->message = $req->message;

		$feedback->save();
		
		return redirect('/feedback-thank-you');
	}
}
