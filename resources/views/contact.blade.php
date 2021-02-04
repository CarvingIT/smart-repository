@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'contact','titlePage'=>'Contact Us'])

@section('content')
@php
	$conf = \App\Sysconfig::all();
	$settings = array();
	foreach($conf as $c){
		$settings[$c->param] = $c->value;
	}
@endphp
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">Contact Us</h4></div>

                <div class="card-body">
					{!! $settings['contact_page'] !!}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
