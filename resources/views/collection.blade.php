@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
    $('#documents').DataTable({
    "aoColumnDefs": [{ "bSortable": false, "aTargets": [0, 4]}],
    "aaSorting": []
    });
} );
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
            <div class="card-header">{{ $collection->name }}
                  <div class="card-header-corner">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE'))
                    <a href="/collection/{{ $collection->id }}/upload"><img class="icon" src="/i/new-document.png" title="New document" /></a>
                  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a href="/collection/{{ $collection->id }}/users"><img class="icon" src="/i/man-user.png" title="Manage users of this collection" /></a>
                  @endif
                  </div>
            </div>
                 <div class="card-body">
                    <p>{{ $collection->description }}</p>
                    <table id="documents" class="display" style="width:100%">
                        <thead>
                            <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($documents as $d)
                    <tr>
                        <td><img class="file-icon" src="/i/file-types/{{ $d->icon() }}.png" alt="{{$d->type}}"/></td>
                        <td>
                        <a href="/document/{{$d->id}}" target="_new">{{ $d->title }}</a>
                        </td>
                        <td>{{ $d->size }}</td>
                        <td>
                        {{ date('F d, Y', strtotime($d->updated_at)) }}
                        <td>
                            @if(Auth::user() && Auth::user()->canEditDocument($d->id))
                            <a href="/document/{{ $d->id }}/edit" title="Create a new revision"><img class="icon" src="/i/pencil-edit-button.png" /></a>
                            @endif
                            <a href="/document/{{ $d->id }}/revisions" title="View revisions"><img class="icon" src="/i/revisions.png" /></a>
                            @if(Auth::user() && Auth::user()->canDeleteDocument($d->id))
                            <a href="/document/{{ $d->id }}/delete" title="Delete"><img class="icon" src="/i/trash.png" /></a>
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
