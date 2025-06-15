<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

class ContactFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

	public $req;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $req)
    {
        $this->req = $req;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('ISA RRR Feedback: '. $this->req->subject)
			->view('emails.contactformsubmission');
    }
}
