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
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

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
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table id="users">
                        <thead>
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
                            <td>e x</td>    <!-- use font awesome icons or image icons -->
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
