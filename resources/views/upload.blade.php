@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $collection->name }} :: Upload Document</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

<form name="document_upload_form" action="/collection/{{ $collection->id }}/upload" method="post" enctype="multipart/form-data">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
@if(!empty($document->id))
<input type="hidden" name="document_id" value="{{ $document->id }}" />
@endif
<div class="form-group row">
<label for="title" class="col-md-4 col-form-label text-md-right">Title</label>
                    <div class="col-md-6">
                    <input type="text" id="title" name="title" size="40" value="@if(!empty($document->id)){{ $document->title }}@endif" 
                    placeholder="If left blank, we shall guess!" />
                    </div>
</div>
<div class="form-group row">
<label for="uploadfile" class="col-md-4 col-form-label text-md-right">Document</label>
    <div class="col-md-6">
    <input id="uploadfile" type="file" name="document">
    </div>
</div>
    @foreach($meta_fields as $f)
    <div class="form-group row">
    <label for="field_{{$f->id}}" class="col-md-4 col-form-label text-md-right">{{$f->label}}</label>
        <div class="col-md-6">
        @if($f->type != 'Select')
        <input id="field_{{$f->id}}" type="text" name="field_{{$f->id}}" />
        @else
        <select id="field_{{$f->id}}" name="field_{{$f->id}}">
            @php
                $options = explode(",", $f->options);
            @endphp
            @foreach($options as $o)
            <option value="{{ $o }}">{{$o}}</option>
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
@endsection
