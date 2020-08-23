@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#collection_users').DataTable({
    "aoColumnDefs": [
            { "bSortable": false, "aTargets": [2]},
     ]
    });
} );
</script>
@endpush

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
            <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Collection users</h4>
            </div>
                 <div class="card-body">
		<div class="row">
                  <div class="col-12 text-right">
                    <a href="/collection/{{ $collection->id }}/user" class="btn btn-sm btn-primary" title="Add User"><i class="material-icons">add</i></a>
                <a href="/collection/{{ $collection->id }}" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                  </div>

                </div>
			<div class="table-responsive">
                    <table id="collection_users" class="display table responsive">
                        <thead class="text-primary">
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
                                    <i class="material-icons">edit</i>
                                    <div class="ripple-container"></div>
                                  </a>

                            <a href="/collection/{{ $collection->id }}/remove-user/{{ ($perms[0]->user)->id }}" class="btn btn-danger btn-link">
                                    <i class="material-icons">close</i>
                                    <div class="ripple-container"></div>
			    </a>
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
		</div> <!-- table-responsive ends -->
                 </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
