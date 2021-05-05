@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'activePage'=>'column-config'])

@push('js')
 <link rel="stylesheet" href="/css/jquery-ui.css">
  <script src="/js/jquery-ui.js"></script>
  <script>
  $( function() {
	  $( "#accordion" ).accordion({
	  	'collapsible': true,
  		'active':false,
		'heightStyle': "content",
  	});
  } );
  </script>
@endpush

@section('content')
@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections')}}</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Settings</h4></div>
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

<form name="column_config_form" action="/collection/{{ $collection->id }}/settings" method="post">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
		@php
			$column_config = json_decode($collection->column_config);
		@endphp
	   	<div class="col-md-12" id="accordion">
		<h4>{{__('Display Columns')}}</h4>
		<div class="form-group row">
           <div class="col-md-3"><input name="type" type="checkbox" value="1" 
			@if(!empty($column_config->type) && $column_config->type == 1) checked="checked" @endif /> {{ __('Type') }}</div>
           <div class="col-md-3"><input name="title" type="checkbox" value="1" 
			@if(!empty($column_config->title) && $column_config->title == 1) checked="checked" @endif /> {{ __('Title') }}</div>
           <div class="col-md-3"><input name="size" type="checkbox" value="1"
			@if(!empty($column_config->size) && $column_config->size == 1) checked="checked" @endif /> {{ __('Size') }}</div>
           <div class="col-md-3"><input name="creation_time" type="checkbox" value="1"
			@if(!empty($column_config->creation_time) && $column_config->creation_time == 1) checked="checked" @endif /> {{ __('Creation time') }}</div>

			@foreach($collection->meta_fields as $m)
           <div class="col-md-3"><input name="meta_fields[]" type="checkbox" value="{{ $m->id }}" 
			@if(!empty($column_config->meta_fields) && in_array($m->id, $column_config->meta_fields)) checked="checked" @endif /> {{ __($m->label) }}</div>
			@endforeach
		</div>

		<h4>{{__('Search Fields')}}</h4>
		<div class="form-group row">
           <div class="col-md-3"><input name="title_search" type="checkbox" value="1" 
			@if(!empty($column_config->title_search) && $column_config->title_search == 1) checked="checked" @endif /> Title</div>

			@foreach($collection->meta_fields as $m)
           <div class="col-md-3"><input name="meta_fields_search[]" type="checkbox" value="{{ $m->id }}" 
			@if(!empty($column_config->meta_fields_search) && in_array($m->id, $column_config->meta_fields_search)) checked="checked" @endif /> {{ $m->label }}</div>
			@endforeach
		</div>

		<h4>{{ __('IMAP Settings (Map an email address to this collection)')}}</h4>
		<div class="form-group">
		<p>{{ __('Attachments sent to this email address will be automatically imported into your collection. You may need the help of your IT staff to fill out the following details.') }}</p>
			<div class="row">
				<div class="col-md-2 text-right">
					<label for="email_address">Email address</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="email_address" id="email_address" placeholder="e.g. knowledge@yourdomain.com" value="" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="imap_server">IMAP server</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="imap_server" id="imap_server" value="" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="server_port">Port</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="server_port" id="server_port" value="" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="security">SSL/TLS</label>
				</div>
				<div class="col-md-10">
					<select name="security" class="selectpicker" id="security">
						<option value="ssl">SSL</option>
						<option value="tls">TLS</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="username">Username</label>
				</div>
				<div class="col-md-4">
					<input type="text" name="username" id="username" value="" />
				</div>
				<div class="col-md-2">
					<label for="password">Password</label>
				</div>
				<div class="col-md-4">
					<input type="password" name="password" id="password" value="" />
				</div>
			</div>
			<div class="row">
			</div>
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
