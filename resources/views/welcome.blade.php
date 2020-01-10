@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'activePage' => 'home', 'title' => __('Smart Repository'), 'titlePage' => 'Collections'])

@section('content')
<div class="container">
<div class="container-fluid">
  <!--div class="row justify-content-center" style="margin-top:1%;"-->
      <div class="col-lg-12 col-md-12">
<div class="card">
<div class="card-header card-header-primary"><h4 class="card-title">Welcome To Smart Repository</h4></div>
<div class="card-body">
          <div style="background:#fff; color:#000; padding:1%; ">
	<p><strong>Document management</strong> is very Easy Now with <strong>Smart Repository</strong>. Until now your organization stores, manages and tracks its electronic documents on google drive or some other external resources. It incorporates document, document repositories, multiple revisions and search systems. Now we are presenting all these facilities in one software. It has some more unique features...</p> 
	<strong>Smart Search</strong> You have definitely heard of Smartphones; have you heard of <strong>Smart Search</strong>?
	<p>Presenting one of its kind Smart Search functionality that enables you to search any text content from your multiple files that is uploaded on the software. The good news is that it doesn’t just search the word in the title but in the entire file as well to generate accurate and suitable Search result.</p>
	<p><strong>What is OCR?</strong> This Smart Search feature uses <strong>OCR (Optical Character Recognition)</strong> at the backend that allows you to get accurate English text content from the images uploaded. For example, if you upload screenshot of your chat on whatsapp, the Search feature allows you to search the text content on the image uploaded also. Isn’t that great? No doubt, this is a one shot solution to your Searching requirements.</p>
	<p><strong>Revisions</strong> Our software not only searches the document but also keeps its multiple versions also.</p>
</div>
</div>
</div>
      </div>
  <!--/div-->
</div>
</div>
@endsection
