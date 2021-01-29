@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'activePage'=>'column-config'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections')}}</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Column Config</h4></div>
                <div class="col-md-12 text-right">
                <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                </div>

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
				<div class="alert alert-<?php echo $msg; ?>">
                        	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        	<i class="material-icons">close</i>
                        	</button>
                        	<span>{{ Session::get('alert-' . $msg) }}</span>
                        </div>

                        @endif
                    @endforeach
                    </div>

<form name="column_config_form" action="/collection/{{ $collection->id }}/column-config" method="post">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
		@php
			$column_config = json_decode($collection->column_config);
		@endphp
	   	<div class="col-md-12">
		<div class="form-group row">
           <div class="col-md-3"><input name="type" type="checkbox" value="1" 
			@if($column_config->type == 1) checked="checked" @endif /> Type</div>
           <div class="col-md-3"><input name="title" type="checkbox" value="1" 
			@if($column_config->title == 1) checked="checked" @endif /> Title</div>
           <div class="col-md-3"><input name="size" type="checkbox" value="1"
			@if($column_config->size == 1) checked="checked" @endif /> Size</div>
           <div class="col-md-3"><input name="creation_time" type="checkbox" value="1"
			@if($column_config->creation_time == 1) checked="checked" @endif /> Creation time</div>
		   </div>
		</div>
<div class="form-group row mb-0">
    <div class="col-md-9 offset-md-4">
        <button type="submit" class="btn btn-primary"> Save </button>
    </div>
</div>

</form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
