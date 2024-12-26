<h3>Contact Form Submission</h3>
<p>
Name: {{ $req->name }}<br />
Address: {!! nl2br($req->address) !!}<br/>
Email: {{ $req->email }}<br/>
Subject: {{ $req->subject }} <br/>
Message: {!! nl2br($req->message) !!}<br/>
</p>

