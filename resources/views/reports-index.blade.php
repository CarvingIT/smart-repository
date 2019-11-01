@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Reports</div>
                <div class="card-body">
                  <ul>
                    <li><a href="/reports/downloads">Downloads</a></li>
                    <li><a href="/reports/uploads">Uploads</a></li>
                  </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection