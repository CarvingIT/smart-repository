@extends('layouts.app')

@section('content')
<div class="container" style="margin-top:5%;">
<div class="container-fluid">
    <!--div class="row justify-content-center"-->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">Reports</div>
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
</div>
@endsection
