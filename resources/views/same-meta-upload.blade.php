@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections') }}</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Upload Document</h4></div>
                <div class="col-md-12 text-right">
                <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                </div>

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
						<div class="alert alert-<?php echo $msg; ?>">
                        	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        	<i class="material-icons">close</i>
                        	</button>
                        	<span>{{ Session::get('alert-' . $msg) }}</span>
                        </div>
                        @endif
                    @endforeach
                    </div>

<form name="document_upload_form" action="/collection/{{ $collection->id }}/upload" method="post" enctype="multipart/form-data">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
<input type="hidden" name="master_document_id" value="{{ $master_document_id }}" />
		<div class="form-group row">
	   	   <div class="col-md-3">
		   <label for="title" class="col-md-12 col-form-label text-md-right">Title</label>
		   </div>
                    <div class="col-md-9">
                    <input class="form-control" type="text" id="title" name="title" size="40" value="@if(!empty($document->id)){{ $document->title }}@endif" 
                    placeholder="If left blank, we shall guess!" />
                    </div>
		</div>
		<div class="form-group row">
		   <div class="col-md-3">
		   <label for="uploadfile" class="col-md-12 col-form-label text-md-right">Document</label>
		   </div>
    		   <div class="col-md-9">
			   <label for='filesize'><font color="red">File size must be less than {{ $size_limit }}B.</font></label>
    		   <input id="uploadfile" type="file" class="form-control-file" name="document" @if(empty($document->id)) required @endif> 
    		   </div>
		</div>
<div class="form-group row mb-0">
    <div class="col-md-9 offset-md-4">
        <button type="submit" class="btn btn-primary"> Save </button>
    </div>
</div>

</form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
