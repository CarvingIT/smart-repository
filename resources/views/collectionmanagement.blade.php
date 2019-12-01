@extends('layouts.app')

@section('content')

<script>
$(document).ready(function() {
    $('#collections').DataTable();
} );
</script>
<div class="container" style="margin-top:5%;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Collections
                    <div class="card-header-corner"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png" style="width:4%;"/></a></div>
                </div>

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                        @endif
                    @endforeach
                    </div>

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
                                <a href="/admin/collection-form/{{$c->id}}"><img class="icon" src="/i/pencil-edit-button.png" style="width:13%;/></a>
                                <a href="/admin/collection-form/{{$c->id}}/delete"><img class="icon" src="/i/trash.png" style="width:13%;" /></a>
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
