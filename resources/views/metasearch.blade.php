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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $collection->name }} :: Metasearch</div>
                <div class="card-body">

<form name="metasearch_form" action="/collection/{{ $collection->id }}/metasearch" method="post">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
    @foreach($collection->meta_fields as $f)
    <div class="form-group row">
    <label for="meta_field_{{$f->id}}" class="col-md-10 col-form-label text-md-left">{{$f->label}}</label>
        <div class="col-md-4">
        <select class="form-control" name="operator_{{$f->id}}">
            @if($f->type == 'Text' || $f->type == 'Select')
            <option value="matches">matches</option>
            <option value="contains">contains</option>
            @elseif($f->type == 'Numeric')
            <option value=">=">greater than or equal to</option>
            <option value="matches">matches</option>
            <option value="<=">less than or equal to</option>
            @elseif($f->type == 'Date')
            <option value=">=">on or later than</option>
            <option value="matches">matches</option>
            <option value="<=">on of before</option>
            @endif
        </select>
        </div>
        <div class="col-md-6">
        @if($f->type == 'Text')
        <input class="form-control" id="meta_field_{{$f->id}}" type="text" name="meta_field_{{$f->id}}" value="" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Numeric')
        <input class="form-control" id="meta_field_{{$f->id}}" type="number" step="0.01" min="-9999999999.99" max="9999999999.99" name="meta_field_{{$f->id}}" value="" placeholder="{{ $f->placeholder }}" />
        @elseif ($f->type == 'Date')
        <input class="form-control" id="meta_field_{{$f->id}}" type="date" name="meta_field_{{$f->id}}" value="" placeholder="{{ $f->placeholder }}" />
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
            <option value="{{$o}}">{{$o}}</option>
            @endforeach
        </select>
        @endif
        </div>
    </div>
    @endforeach
<div class="form-group row mb-0">
    <div class="col-md-8 offset-md-4">
        <button type="submit" class="btn btn-primary"> Search </button>
    </div>
</div>

</form>
                </div>
            </div>

    <div class="card">
        <div class="card-header">Metasearch Results</div>
        <div class="card-body">
              <table id="documents" class="display" style="width:100%">
                  <thead>
                      <tr>
                      <th>Type</th>
                      <th>Title</th>
                      <th class="dt-right">Size</th>
                      <th>Created</th>
                      <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                @foreach($documents as $r)
                @php
                $d = \App\Document::find($r->id);
                @endphp
                <tr>
                    <td><img class="file-icon" src="/i/file-types/{{$d->icon()}}.png" /></td>
                    <td>{{$d->title}}</td>
                    <td data-order="{{$d->size}}">{{$d->human_filesize()}}</td>
                    <td data-order="{{$d->updated_at}}">{{ date('F d, Y', strtotime($d->updated_at)) }}</td>
                    <td>
                <a href="/document/{{$d->id}}/revisions" title="View revisions"><img class="icon" src="/i/revisions.png" /></a>
                @if(Auth::user())
                @if(Auth::user()->canEditDocument($d->id))
                <a href="/document/{{$d->id}}/edit" title="Create a new revision"><img class="icon" src="/i/pencil-edit-button.png" /></a>
                @endif
                @if(Auth::user()->canDeleteDocument($d->id))
                <a href="/document/{{$d->id}}/delete" title="Delete document"><img class="icon" src="/i/trash.png" /></a>
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
@endsection
