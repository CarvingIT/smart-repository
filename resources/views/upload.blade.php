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
@if(!empty($document->id))
<input type="hidden" name="document_id" value="{{ $document->id }}" />
@endif
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
		@if(!empty($document->id))
		<div class="form-group row">
		   <div class="col-md-3">
		   <label for="uploadfile" class="col-md-12 col-form-label text-md-right">Uploaded Document</label>
		   </div>
    		   <div class="col-md-9">
			@if(!empty($document->id))<a href="/document/{{ $document->id }}" target="_blank">{{ $document->title }} </a> @endif
    		   </div>
		</div>
		@endif
@if(!empty($document->id) && Auth::user()->canApproveDocument($document->id))
@if(count($collection_has_approval)==0)
@else
		<div class="form-group row">
		   <div class="col-md-3">
		   <label for="approved" class="col-md-12 col-form-label text-md-right">Document Status</label>
		   </div>
    		   <div class="col-md-9">
    		   <input id="approved_on" type="checkbox" name="approved_on" value="1" @if(!empty($document->approved_on)) checked @endif /> Approved
    		   </div>
		</div>
@endif
@endif
    @foreach($collection->meta_fields as $f)
    <div class="form-group row">
		   <div class="col-md-3">
    			<label for="meta_field_{{$f->id}}" class="col-md-12 col-form-label text-md-right">{{$f->label}}</label>
    		   </div>
        <div class="col-md-9">
        @if($f->type == 'Text')
        <input class="form-control" id="meta_field_{{$f->id}}" type="text" name="meta_field_{{$f->id}}" value="{{ $document->meta_value($f->id) }}" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Numeric')
        <input class="form-control" id="meta_field_{{$f->id}}" type="number" step="0.01" min="-9999999999.99" max="9999999999.99" name="meta_field_{{$f->id}}" value="{{ $document->meta_value($f->id) }}" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Date')
        <input id="meta_field_{{$f->id}}" type="date" name="meta_field_{{$f->id}}" value="{{ $document->meta_value($f->id) }}" placeholder="{{ $f->placeholder }}" />

        @elseif (in_array($f->type, array('Select', 'MultiSelect')))
        <select class="form-control selectpicker" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}[]" @if($f->type == 'MultiSelect') multiple="multiple" @endif>
            @php
                $options = explode(",", $f->options);
				sort($options);
            @endphp
            <option value="">{{ $f->placeholder }}</option>
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
                @endphp
				@if($f->type == 'MultiSelect')
            	<option value="{{$o}}" @if(in_array($o, json_decode($document->meta_value($f->id)))) selected="selected" @endif >{{$o}}</option>
				@else
            	<option value="{{$o}}" @if($o == $document->meta_value($f->id)) selected="selected" @endif >{{$o}}</option>
				@endif
            @endforeach
        </select>
		@elseif ($f->type == 'SelectCombo')
		<input type="text" class="form-control" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}" value="{{ $document->meta_value($f->id) }}" autocomplete="off" list="optionvalues" placeholder="{{ $f->placeholder }}" />
		<label>You can select an option or type custom text above.</label>
		<datalist id="optionvalues">
            @php
                $options = explode(",", $f->options);
				sort($options);
            @endphp
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
                @endphp
            <option>{{$o}}</option>
            @endforeach
		</datalist>
        @endif
        </div>
    </div>
    @endforeach
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
