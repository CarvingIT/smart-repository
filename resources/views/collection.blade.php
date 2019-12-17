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

<div class="container" style="margin-top:5%;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
            <div class="card-header"><a href="/collections">Collections</a> :: {{ $collection->name }}
                  <div class="card-header-corner">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a href="/collection/{{ $collection->id }}/users"><img class="icon" src="/i/man-user.png" title="Manage users of this collection" style="width:3%;"/></a>
                    <a href="/collection/{{ $collection->id }}/meta"><img class="icon" src="/i/meta.png" title="Manage meta information fields of this collection" style="width:3%;"/></a>
                  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE'))
                    <a href="/collection/{{ $collection->id }}/upload"><img class="icon" src="/i/new-document.png" title="New document" style="width:3%;"/></a>
                  @endif
                  @if(count($collection->meta_fields)>0)
                    <a href="/collection/{{ $collection->id }}/metasearch"><img class="icon" src="/i/meta_search.png" title="Meta search" /></a>
                  @endif
                  </div>
            </div>
                 <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                        @endif
                    @endforeach
                    </div>
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
			<tbody>
                        @foreach ($documents as $c)
                        <tr>
                            <td>{{ $c->type }}</td>
                            <td>{{ $c->title }}</td>
                            <td>{{ $c->size }}</td>
                            <td>{{ $c->created_at }}</td>
                            <td class="td-actions text-right">
                                <a rel="tooltip" class="btn btn-success btn-link" href="/admin/collection-form/{{$c->id}}">
                                <!--img class="material-icons" src="/i/pencil-edit-button.png" width=20% /-->
                                <i class="material-icons">edit</i>
                                <div class="ripple-container"></div>
                                </a>
                                <a rel="tooltip" class="btn btn-danger btn-link" href="/admin/collection-form/{{$c->id}}/delete">
                                <!--img class="material-icon" src="/i/trash.png" style="width:20%;" /-->
                                <i class="material-icons">close</i>
                                <div class="ripple-container"></div>
                                </a>
                            </td>    <!-- use font awesome icons or image icons -->
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
