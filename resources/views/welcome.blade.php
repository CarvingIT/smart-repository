@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'activePage' => 'home', 'title' => __('Smart Repository'), 'titlePage' => 'Collections'])

@section('content')
<div class="container" style="height: auto;">
<div class="container-fluid">
  <div class="row justify-content-center">
      <div class="col-lg-7 col-md-8">
          <!--h1 class="text-white text-center">{{ __('Smart Repository.') }}</h1-->
      </div>
  </div>
  <div class="row justify-content-center" style="margin-top:3%;">
      <div class="col-lg-12 col-md-12">
          <div style="background:#fff; color:#000; padding:5%; ">
	<p><strong>Document management</strong> is how your organization stores, manages and tracks its electronic documents. It incorporates document and content capture, workflow, document repositories, multiple revisions and search systems.</p> 
	<strong>Smart Search</strong> You have definitely heard of Smartphones; have you heard of <strong>Smart Search</strong>?
<p>Presenting one of its kind Smart Search functionality that enables you to search any text content from your multiple files that is uploaded on the software. The good news is that it doesn’t just search the word in the title but in the entire file as well to generate accurate and suitable Search result.</p>
<p>	<strong>What is OCR?</strong> This Smart Search feature uses <strong>OCR (Optical Character Recognition)</strong> at the backend that allows you to get accurate English text content from the images uploaded. For example, if you upload screenshot of your chat on whatsapp, the Search feature allows you to search the text content on the image uploaded also. Isn’t that great? No doubt, this is a one shot solution to your Searching requirements.</p>
</div>
      </div>
  </div>
</div>
</div>
@endsection
