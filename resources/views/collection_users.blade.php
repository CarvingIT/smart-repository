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
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Collection users</h4>
                <!--div class="card-header-corner"><a href="/collection/{{ $collection->id }}/user"><img class="icon" src="/i/plus.png"></a></div-->
            </div>
                 <div class="card-body">
		<div class="row">
                  <div class="col-12 text-right">
                    <a href="/collection/{{ $collection->id }}/user" class="btn btn-sm btn-primary">{{ __('Add user') }}</a>
                  </div>
                </div>

                    <table id="collection_users" class="display table responsive" style="width:100%">
                        <thead class=" text-primary">
                            <tr>
                            <th>ID</th>
                            <th>Permissions</th>
                            <th class="text-right">Actions</th>
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
                        <td class="td-actions text-right">
                            <a rel="tooltip" class="btn btn-success btn-link" href="/collection/{{ $collection->id }}/user/{{ ($perms[0]->user)->id }}" data-original-title="" title="">
				<!--img class="icon" src="/i/pencil-edit-button.png" /-->
                                    <i class="material-icons">edit</i>
                                    <div class="ripple-container"></div>
                                  </a>

                            <a href="/collection/{{ $collection->id }}/remove-user/{{ ($perms[0]->user)->id }}" class="btn btn-danger btn-link">
				<!--img class="icon" src="/i/trash.png" /-->
                                    <i class="material-icons">close</i>
                                    <div class="ripple-container"></div>
			    </a>
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
