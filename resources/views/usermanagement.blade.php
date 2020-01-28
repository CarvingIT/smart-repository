@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
    $('#users').DataTable();
} );
</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add User</div>

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
			<div class="alert alert-success">
                    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      	<i class="material-icons">close</i>
                    	</button>
                        <span>{{ Session::get('alert-' . $msg) }}</span>
                  	</div>

                        @endif
                    @endforeach
                    </div>

                   <form method="post" action="/admin/saveuser">
                    @csrf()
                   <label for="name">Name</label> 
                   <input type="text" name="name" id="name" value="" />
                   <label for="email">Email</label> 
                   <input type="text" name="email" id="email" value="" />

                   <input type="submit" value="Submit" />
                   </form> 
                </div>
            </div>

            <div class="card">
                <div class="card-header">Users</div>

                <div class="card-body">

                    <table id="users">
                        <thead class=" text-primary">
                            <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                            <a href="/admin/user/{{ $u->id }}/edit"><img class="icon" src="/i/pencil-edit-button.png" /></a>
                            <a href="/admin/user/{{ $u->id }}/delete"><img class="icon" src="/i/trash.png" /></a>
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
