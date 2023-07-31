@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','titlePage'=>'ERROR', 'activePage'=>'Error'])

@section('content')
<div class="container">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
		<div class="card-header card-header-primary">
		<h4 class="card-title">Not found</h4></div>
                <div class="card-body">
		<h3>{{ $exception->getMessage() }}</h3>
		<p>The page that you are looking for is not found here.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
