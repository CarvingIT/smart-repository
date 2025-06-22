@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Smart Repository'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">Reports</h4></div>
                <div class="card-body">
                  <ul>
                    <li><a href="/reports/downloads">Downloads</a></li>
                    <li><a href="/reports/uploads">Uploads</a></li>
                    <li><a href="/reports/search-queries">Search Queries</a></li>
                    <li><a href="/reports/duplicates">Duplicates</a></li>
                  </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
