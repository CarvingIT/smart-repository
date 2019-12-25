@extends('layouts.app')

@section('content')

<script>
$(document).ready(function() {
    $('#collections').DataTable();
} );
</script>
<div class="container" style="margin-top:5%;">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Collections</h4>
                    <div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div>
              </div>		

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                        @endif
                    @endforeach
                    </div>
			<div class="table-responsive">
                    <table id="collections" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($collections as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->type }}</td>
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
</div>
</div>
@endsection
