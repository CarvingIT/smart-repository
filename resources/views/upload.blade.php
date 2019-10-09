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
<div class="input-with-label">
<label for="title">Title (Optional)</label>
<input type="text" id="title" name="title" size="40" value="@if(!empty($document->id)){{ $document->title }}@endif" 
        placeholder="Give your document a title. If left blank, we shall guess!" />
</div>
<input type="file" name="document">
<button type="submit">Upload</button>
</form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
