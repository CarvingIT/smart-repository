@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
    $('#documents').DataTable({
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [0, 4]},
            { "className": 'dt-right', "aTargets": [2]}
     ],
    "searching": false,
    "order": [[ 3, "desc" ]],
    });
} );
</script>

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Advanced Search</h4></div>
                <div class="card-body">

<form name="metasearch_form" action="/collection/{{ $collection->id }}/metasearch" method="get">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
    @foreach($collection->meta_fields as $f)
    <div class="form-group row">
	<div class="col-md-4">
    <label for="meta_field_{{$f->id}}" class="col-md-6 col-form-label text-md-left">{{$f->label}}</label>
	</div>
        <div class="col-md-4">
        <select class="form-control" name="operator_{{$f->id}}">
            @if($f->type == 'Text')
            <option value="=" @if(@$params['operator_'.$f->id] == "=") selected @endif>matches</option>
            <option value="like" @if(@$params['operator_'.$f->id] == "like") selected @endif>contains</option>
            @elseif($f->type == 'Select')
            <option value="=" @if(@$params['operator_'.$f->id] == "=") selected @endif>matches</option>
            @elseif($f->type == 'Numeric')
            <option value=">=" @if(@$params['operator_'.$f->id] == ">=") selected @endif>greater than or equal to</option>
            <option value="=" @if(@$params['operator_'.$f->id] == "=") selected @endif>matches</option>
            <option value="<=" @if(@$params['operator_'.$f->id] == "<=") selected @endif>less than or equal to</option>
            @elseif($f->type == 'Date')
            <option value=">=" @if(@$params['operator_'.$f->id] == ">=") selected @endif>on or later than</option>
            <option value="=" @if(@$params['operator_'.$f->id] == "=") selected @endif>matches</option>
            <option value="<=" @if(@$params['operator_'.$f->id] == "<=") selected @endif>on or before</option>
            @endif
        </select>
        </div>
        <div class="col-md-4">
        @if($f->type == 'Text')
        <input class="form-control" id="meta_field_{{$f->id}}" type="text" name="meta_field_{{$f->id}}" value="{{ @$params['meta_field_'.$f->id] }}" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Numeric')
        <input class="form-control" id="meta_field_{{$f->id}}" type="number" step="0.01" min="-9999999999.99" max="9999999999.99" name="meta_field_{{$f->id}}" value="{{ @$params['meta_field_'.$f->id] }}" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Date')
        <input class="form-control" id="meta_field_{{$f->id}}" type="date" name="meta_field_{{$f->id}}" value="{{ @$params['meta_field_'.$f->id] }}" placeholder="{{ $f->placeholder }}" />
        @else
        <select class="form-control" id="meta_field_{{$f->id}}" name="meta_field_{{$f->id}}">
            @php
                $options = explode(",", $f->options);
            @endphp
            <option value="">Any</option>
            @foreach($options as $o)
                @php
                    $o = ltrim(rtrim($o));
                @endphp
            <option value="{{$o}}" @if(@$params['meta_field_'.$f->id] == $o) selected @endif>{{$o}}</option>
            @endforeach
        </select>
        @endif
        </div>
    </div>
    @endforeach
    <div class="form-group row">
	<div class="col-md-4">
    	<label for="content_field" class="col-md-6 col-form-label text-md-left">Content to be searched.</label>
	</div>
        <div class="col-md-4">
        <input class="form-control" id="text_field_operator" type="text" name="content_field_operator" value="like" placeholder="Content to be searched operator">
        <input class="form-control" id="text_field" type="text" name="content_field" value="{{ @$params['content_field'] }} " placeholder="Content to be searched">
	</div>
	</div>
<div class="form-group row mb-0">
    <div class="col-md-8 offset-md-4">
        <button type="submit" class="btn btn-primary"> Search </button>
    </div>
</div>

</form>
                </div>
            </div>

    <div class="card">
        <div class="card-header card-header-primary"><h4 class="card-title">Advanced Search Results</h4></div>
        <div class="card-body">
              <table id="documents" class="display table" style="width:100%">
                  <thead class=" text-primary">
                      <tr>
                      <th>Type</th>
                      <th>Title</th>
                      <th class="dt-right">Size</th>
                      <th>Created</th>
                      <th class="text-right td-actions">Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                @foreach($documents as $r)
                @php
                $d = \App\Document::find($r);
                @endphp
                <tr>
                    <td><img class="file-icon" src="/i/file-types/{{$d->icon()}}.png" /></td>
                    <td>{{$d->title}}</td>
                    <td data-order="{{$d->size}}">{{$d->human_filesize()}}</td>
                    <td data-order="{{$d->updated_at}}">{{ date('F d, Y', strtotime($d->updated_at)) }}</td>
                    <td class="text-right td-actions">
                <a href="/document/{{$d->id}}/revisions" title="View revisions" class="btn btn-primary btn-link">
		<i class="material-icons">view_column</i>
                <div class="ripple-container"></div>
		<!--img class="icon" src="/i/revisions.png" /-->
		</a>
                @if(Auth::user())
                @if(Auth::user()->canEditDocument($d->id))
                <a href="/document/{{$d->id}}/edit" title="Create a new revision" class="btn btn-success btn-link">
		<i class="material-icons">edit</i>
                <div class="ripple-container"></div>
		<!--img class="icon" src="/i/pencil-edit-button.png" /-->
		</a>
                @endif
                @if(Auth::user()->canDeleteDocument($d->id))
                <a href="/document/{{$d->id}}/delete" title="Delete document" class="btn btn-danger btn-link">
		<i class="material-icons">close</i>
                <div class="ripple-container"></div>
		<!--img class="icon" src="/i/trash.png" /-->
		</a>
                @endif
                @endif
                    </td>
                </tr>
                @endforeach
                  </tbody>
              </table>
        </div>
    </div>

        </div>
    </div>
</div>
</div>
@endsection
