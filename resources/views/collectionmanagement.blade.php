@extends('layouts.app')

@section('content')

<script>
$(document).ready(function() {
    $('#collections').DataTable();
} );
</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Collections
                    <div class="card-header-corner"><a href="/admin/collection-form/new">Add</a></div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table id="collections" class="display" style="width:100%">
                        <thead>
                            <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($collections as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->type }}</td>
                            <td>{{ $c->created_at }}</td>
                            <td>
                                <a href="/admin/collection-form/{{$c->id}}">e</a> x
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
