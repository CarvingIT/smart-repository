@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
    $('#documents').DataTable({
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [0, 4]},
            { "className": 'dt-right', "aTargets": [2]}
     ],
    "order": [[ 3, "desc" ]],
    "serverSide":true,
    "ajax":'/collection/{{$collection->id}}/search',
    "columns":[
        {data:"type"},
        {data:"title"},
        {data:"size",
            render:{
                '_': 'display',
                'sort': 'bytes'
            }
        },
        {data:"updated_at",
            render:{
                '_':'display',
                'sort': 'updated_date'
            }
        },
        {data:"actions"},
    ]
    });
} );
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
            <div class="card-header">{{ $collection->name }}
                  <div class="card-header-corner">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a href="/collection/{{ $collection->id }}/users"><img class="icon" src="/i/man-user.png" title="Manage users of this collection" /></a>
                    <a href="/collection/{{ $collection->id }}/meta"><img class="icon" src="/i/meta.png" title="Manage meta information fields of this collection" /></a>
                  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE'))
                    <a href="/collection/{{ $collection->id }}/upload"><img class="icon" src="/i/new-document.png" title="New document" /></a>
                  @endif
                    <a href="/collection/{{ $collection->id }}/metasearch"><img class="icon" src="/i/meta_search.png" title="Meta search" /></a>
                  </div>
            </div>
                 <div class="card-body">
                    <p>{{ $collection->description }}</p>
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
                    </table>
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection
