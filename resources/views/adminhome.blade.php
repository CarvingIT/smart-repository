@extends('layouts.app',['class' => 'off-canvas-sidebar', 'title' => 'Smart Repository'])

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Admin Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <i class="material-icons">close</i>
                    </button>
                    <span>{{ session('status') }}</span>
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
