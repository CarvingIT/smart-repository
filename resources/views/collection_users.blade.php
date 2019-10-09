@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
    $('#collection_users').DataTable();
} );
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
            <div class="card-header">{{ $collection->name }} :: Collection users</div>
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
                        <td>
                        {{ ($perms[0]->user)->email }}
                        </td>
                        <td>
                        @foreach($perms as $p)
                            {{ ($p->permission)->name }}, 
                        @endforeach
                        </td>
                        <td>
                            <a href="#"><img class="icon" src="/i/pencil-edit-button.png" /></a>
                            <a href="#"><img class="icon" src="/i/trash.png" /></a>
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
