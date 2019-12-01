@extends('layouts.app')

@section('content')
<div class="container" style="margin-top:5%">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Admin Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                  <ul>
                    <li><a href="/admin/collectionmanagement">Manage Collections</a></li>
                    <li><a href="/admin/usermanagement">Manage Users</a></li>
                  </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
