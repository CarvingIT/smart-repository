@extends('layouts.app')

@section('content')
<div class="container" style="margin-top:5%;">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Upload Document</h4></div>

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
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
    <div class="col-md-4">
<label for="title" class="col-md-8 col-form-label text-md-right">Title</label>
</div>
                    <div class="col-md-6">
                    <input class="form-control" type="text" id="title" name="title" size="40" value="@if(!empty($document->id)){{ $document->title }}@endif" 
                    placeholder="If left blank, we shall guess!" />
                    </div>
</div>
<div class="form-group row">
    <div class="col-md-4">
<label for="uploadfile" class="col-md-8 col-form-label text-md-right">Document</label>
	</div>
    <div class="col-md-4">
    <input id="uploadfile" type="file" name="document">
    </div>
</div>
    @foreach($collection->meta_fields as $f)
    <div class="form-group row">
    <label for="meta_field_{{$f->id}}" class="col-md-4 col-form-label text-md-right">{{$f->label}}</label>
        <div class="col-md-6">
        @if($f->type == 'Text')
        <input class="form-control" id="meta_field_{{$f->id}}" type="text" name="meta_field_{{$f->id}}" value="{{ $document->meta_value($f->id) }}" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Numeric')
        <input class="form-control" id="meta_field_{{$f->id}}" type="number" step="0.01" min="-9999999999.99" max="9999999999.99" name="meta_field_{{$f->id}}" value="{{ $document->meta_value($f->id) }}" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Date')
        <input class="form-control" id="meta_field_{{$f->id}}" type="date" name="meta_field_{{$f->id}}" value="{{ $document->meta_value($f->id) }}" placeholder="{{ $f->placeholder }}" />
        @else
        <select class="form-control" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}">
            @php
                $options = explode(",", $f->options);
            @endphp
            <option value="">{{ $f->placeholder }}</option>
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
                @endphp
            <option value="{{$o}}" @if($o == $document->meta_value($f->id)) selected="selected" @endif >{{$o}}</option>
            @endforeach
        </select>
        @endif
        </div>
    </div>
    @endforeach
<div class="form-group row mb-0">
    <div class="col-md-8 offset-md-4">
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
