@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'activePage' => 'home', 'title' => __('Smart Repository')])

@section('content')
<div class="container" style="height: auto;">
  <div class="row justify-content-center">
      <div class="col-lg-7 col-md-8">
          <h1 class="text-white text-center">{{ __('Welcome to Smart Repository.') }}</h1>
      </div>
  </div>
  <div class="row justify-content-center">
      <div class="col-lg-7 col-md-8">
          <p style="background:#fff; color:#000; padding:5%;">You have definitely heard of Smartphones; have you heard of Smart Search?

Presenting one of its kind Smart Search functionality that enables you to search any text content from your multiple files that is uploaded on the software. The good news is that it doesn’t just search the word in the title but in the entire file as well to generate accurate and suitable Search result. We currently support pdf, doc, docx, xls, xlsx, ppt, pptx and all text file formats.</p>
      </div>
  </div>
</div>
@endsection
