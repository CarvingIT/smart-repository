@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h4>{{ $collection->name }} :: Upload Document</h4></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

<form name="document_upload_form" action="/collection/{{ $collection->id }}/upload" method="post" enctype="multipart/form-data">
@csrf()
<table><tr>
<td>
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
<input type="file" name="document"></td>
<td>
<button type="submit">Upload</button>
</td>
</tr></table>
</form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
