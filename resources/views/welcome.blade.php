@extends('layouts.app', ['class' => 'off-canvas-sidebar','activePage' => 'home', 'title' => __('DEMO SITE'), 'titlePage' => 'Collections'])

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
			<div class="card-header card-header-primary"><h4 class="card-title">{{ env('APP_NAME') }}</h4></div>
			<div class="card-body">
				<div class="row justify-content-center">
				@if(!empty($settings['banner_image_1']))
    			<img class="img-responsive" src="{{ $settings['banner_image_1'] }}" />
				@endif
				</div>
				{!! $settings['home_page'] !!}
			</div>
		</div>
      </div>
</div>
</div>
</div>
@endsection
