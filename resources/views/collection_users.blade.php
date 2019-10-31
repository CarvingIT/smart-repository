@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
    $('#collection_users').DataTable({
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [2]},
     ]
    });
} );
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
            <div class="card-header"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Collection users
                <div class="card-header-corner"><a href="/collection/{{ $collection->id }}/user"><img class="icon" src="/i/plus.png"></a></div>
            </div>
                 <div class="card-body">
                    <table id="collection_users" class="display" style="width:100%">
                        <thead>
                            <tr>
                            <th>ID</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($collection_users as $user_id=>$perms)
                    <tr>
                        <td valign="top">
                        {{ ($perms[0]->user)->email }}
                        </td>
                        <td>
                        @foreach($perms as $p)
                            {{ ($p->permission)->name }}<br/>
                        @endforeach
                        </td>
                        <td>
                            <a href="/collection/{{ $collection->id }}/user/{{ ($perms[0]->user)->id }}"><img class="icon" src="/i/pencil-edit-button.png" /></a>
                            <a href="/collection/{{ $collection->id }}/remove-user/{{ ($perms[0]->user)->id }}"><img class="icon" src="/i/trash.png" /></a>
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
