@extends('layouts.app', ['class' => 'off-canvas-sidebar','activePage' => 'home', 'title' => __('Smart Repository'), 'titlePage' => 'Collections'])

@section('content')
<div class="container">
<div class="container-fluid">
<div class="row justify-content-center">
      <div class="col-md-12">
<div class="card">
<div class="card-header card-header-primary"><h4 class="card-title">Smart Repository</h4></div>
<div class="card-body">
    <img class="img-responsive" style="float:left;" src="/i/wordcloud.png" />
    <h3>Welcome to Smart Repository!</h3>
<p>Smart Repository is designed to be an <strong>Institutional Knowledge Repository</strong>. This tool has powerful features like Collaborative management, Cataloging of electronic content, Custom meta fields and filters, Full-text search (search through the documents), Optical Character Recognition (OCR), Tracking of Revisions and strong Permission Control. 
</p>
	<strong>Smart Search</strong>: You have definitely heard of Smartphones; have you heard of <strong>Smart Search</strong>?
	<p>Presenting one of its kind Smart Search functionality that enables you to search any text content from your multiple files that is uploaded on the software. The good news is that it doesn’t just search the word in the title but in the entire file as well to generate accurate and suitable Search result.</p>
	<p><strong>What is OCR?</strong> This Smart Search feature uses <strong>OCR (Optical Character Recognition)</strong> at the backend that allows you to get accurate English text content from the images uploaded. For example, if you upload screenshot of your chat on whatsapp, the Search feature allows you to search the text content on the image uploaded also. Isn’t that great? No doubt, this is a one shot solution to your Searching requirements.</p>
    <p>For a full list of features, click <a href="/features">here</a>.</p>
</div>
</div>
      </div>
  </div>
</div>
</div>
@endsection
