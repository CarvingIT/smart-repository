@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/js/jquery-3.3.1.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#revisions').DataTable({
    "order": [[ 1, "desc" ]],
    "columnDefs":[
        {"targets":[1,3,4], "className":'dt-right'},
        {"targets":[0,1,2,3,4], "bSortable":false}
    ]
    });
} );
</script>
@endpush

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary"><h4 class="card-title">{{ ($document_revisions[0]->document)->title }}</h4></div>
                 <div class="card-body">
			<div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/collection/{{ $collection_id }}" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>

                    <table id="revisions" class="display table" style="width:100%">
                        <thead class=" text-primary">
                            <tr>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Created By</th>
                            <th>Size</th>
                            <th>Changes</th>
                            </tr>
                        </thead>
                        <tbody>
                    @php
                        $i=0;
                    @endphp
                    @foreach($document_revisions as $dr)
                        @php
                        $i++;
                        @endphp
                    <tr>
                        <td><img class="file-icon" src="/i/file-types/{{ ($dr->document)->icon($dr->path) }}.png" /></td>
                        <td data-order="{{ $dr->created_at }}">
                        <a href="/document-revision/{{$dr->id}}" target="_new">{{ date('F d, Y', strtotime($dr->created_at)) }}</a>
                        </td>
                        <td>{{ ($dr->user)->email }}</td>
                        <td data-order="{{$dr->size}}">{{ ($dr->document)->human_filesize($dr->size) }}</td>
                        <td>
                            @if(count($document_revisions) != $i)
                            <a href="/document/{{ $document_revisions[0]->document->id }}/revision-diff/{{ $document_revisions[$i]->id }}/{{ $dr->id }}">
                            <i rev-id="{{$dr->id}}" class="loaddiff material-icons">compare</i>
                            </a>
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
