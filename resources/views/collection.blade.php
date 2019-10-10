@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
    $('#documents').DataTable();
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
                    {{ $collection->description }}
                 </div>
            </div>
            <div class="card">
            <div class="card-header">Documents</div>
                 <div class="card-body">
                    {{$documents->links() }}
                    <table id="documents" class="display" style="width:100%">
                        <thead>
                            <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($documents as $d)
                    <tr>
                        <td>
                        <a href="/document/{{$d->id}}" target="_new">{{ $d->title }}</a>
                        </td>
                        <td>{{ $d->type }}</td>
                        <td>{{ $d->size }}</td>
                        <td>{{ $d->created_at }}</td>
                        <td>
                            @if(Auth::user() && Auth::user()->canEditDocument($d->id))
                            <a href="/document/{{ $d->id }}/edit"><img class="icon" src="/i/pencil-edit-button.png" /></a>
                            @endif
                            @if(Auth::user() && Auth::user()->canDeleteDocument($d->id))
                            <a href="/document/{{ $d->id }}/delete"><img class="icon" src="/i/trash.png" /></a>
                            @endif
                            <a href="/document/{{ $d->id }}/revisions">r</a>
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
