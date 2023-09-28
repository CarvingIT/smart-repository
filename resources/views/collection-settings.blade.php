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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $("#selectsequence").select2();
	$('#selectsequence').on("select2:select", function (evt) {
		var element = evt.params.data.element;
  		var $element = $(element);
  		$element.detach();
  		$(this).append($element);
  		$(this).trigger("change");
	});
});
  </script>

@endpush

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">{{ __('Database')}} :: Configuration</h4></div>
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
			$permissions = \App\Permission::all();
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
			@if($m->type == 'Textarea')
				@continue
			@endif
           <div class="col-md-3"><input name="meta_fields[]" type="checkbox" value="{{ $m->id }}" 
			@if(is_array(@$column_config->meta_fields) && in_array($m->id, $column_config->meta_fields)) checked="checked" @endif /> {{ __($m->label) }}</div>
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

		<h4>{{__('Default permissions to Authenticated Users')}}</h4>
		<div class="form-group row">
			@foreach($permissions as $p)
			@if ($p->name == 'MAINTAINER')
				@continue
			@endif
           <div class="col-md-3"><input name="auth_user_permissions[]" type="checkbox" value="{{ $p->name }}" 
			@if(!empty($column_config->auth_user_permissions) && in_array($p->name, $column_config->auth_user_permissions)) checked="checked" @endif /> {{ $p->description }}</div>
			@endforeach
		</div>

		<h4>{{__('Document Approval Flow')}}</h4>
		<div class="form-group row">
			<select class="selectsequence" id="selectsequence" name="approved_by[]" multiple style="width:100%;">	
				@if(!empty($column_config->approved_by))
					@foreach($column_config->approved_by as $approver)
						@foreach($roles as $role)
							@if(!empty($column_config->approved_by) && $approver == $role->id)
								<option value="{{ $role->id }}" @if(!empty($column_config->approved_by) && $role->id == $approver) selected @endif>{{ $role->name }}</option>
							@endif
						@endforeach
			        	@endforeach
				@else
					@foreach($roles as $role)
						<option value="{{ $role->id }}">{{ $role->name }}</option>
					@endforeach
				@endif
			</select>
		</div>

		<h4>{{__('Info page')}}</h4>
		<div class="form-group row">
           <div class="col-md-3"><input name="show_word_cloud" type="checkbox" value="1" 
			@if(!empty($column_config->show_word_cloud) && $column_config->show_word_cloud == 1) checked="checked" @endif /> Show word cloud</div>
           <div class="col-md-3"><input name="show_audit_trail" type="checkbox" value="1" 
			@if(!empty($column_config->show_audit_trail) && $column_config->show_audit_trail == 1) checked="checked" @endif /> Show audit trail</div>
			<hr />
           <div class="col-md-12 row">
			<div class="col-md-5"><h5>Current label</h5></div>
			<div class="col-md-2"><h5>Hide Label?</h5></div>
			<div class="col-md-2"><h5>Hide Field?</h5></div>
			<div class="col-md-3"><h5>Label override</h5></div>
			</div>
			
			@foreach($collection->meta_fields as $m)
           <div class="col-md-12 row">
			<div class="col-md-5">{{ $m->label }}</div>
			@php 
				$display_label = 'meta_display_label_'.$m->id;
			@endphp
			<div class="col-md-2">
			<input name="meta_hide_label[]" type="checkbox" value="{{$m->id}}" 
			@if(is_array(@$column_config->meta_hide_label) && in_array($m->id, @$column_config->meta_hide_label)) checked="checked" @endif />
			</div>

			<div class="col-md-2">
			<input name="meta_hide_field[]" type="checkbox" value="{{$m->id}}" 
			@if(is_array(@$column_config->meta_hide_field) && in_array($m->id, @$column_config->meta_hide_field)) checked="checked" @endif />
			</div>

			<div class="col-md-3">
			<input name="meta_display_label_{{ $m->id }}" type="text" value="{{ @$column_config->{$display_label} }}" placeholder="Label for display" />
			</div>

			</div>
			@endforeach
		</div>

		<h4>{{__('Notifications')}}</h4>
		<div class="form-group row">
			<div class="col-md-2 text-right">
				<label for="slack_webhook"><img src="/i/Slack_Mark_Web.png" class="icon"/>Slack Webhook</label>
			</div>
			<div class="col-md-10">
				<input type="text" class="form-control" name="slack_webhook" id="slack_webhook" placeholder="Slack webhook url" value="@if(!empty($column_config->slack_webhook)) {{ $column_config->slack_webhook }} @endif" />
			</div>
		</div>

		<h4>{{ __('IMAP Settings (Map an email address to this collection)')}}</h4>
		<div class="form-group">
		<p>{{ __('Attachments sent to this email address will be automatically imported into your collection. You may need the help of your IT staff to fill out the following details.') }}</p>
			<div class="row">
				<div class="col-md-2 text-right">
					<label for="email_address">Email address</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="email_address" id="email_address" placeholder="e.g. knowledge@yourdomain.com" value="@if(!empty($mailbox->address)) {{ $mailbox->address }} @endif" />
				</div>
			</div>
		@php
			$creds = false;
			if(!empty($mailbox->credentials)){
				$creds = json_decode($mailbox->credentials);
			}
		@endphp
			<div class="row">
				<div class="col-md-2">
					<label for="imap_server">IMAP server</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="imap_server" id="imap_server" value="@if($creds) {{ $creds->server_address }} @endif" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="server_port">Port</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="server_port" id="server_port" value="@if($creds) {{ $creds->server_port }} @endif" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="security">SSL/TLS</label>
				</div>
				<div class="col-md-10">
					<select name="security" class="selectpicker" id="security">
						<option value="">Security</option>
						<option value="ssl" @if($creds && $creds->security == 'ssl') {{ 'selected' }} @endif>SSL</option>
						<option value="tls" @if($creds && $creds->security == 'tls') {{ 'selected' }} @endif>TLS</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="username">Username</label>
				</div>
				<div class="col-md-4">
					<input type="text" name="username" id="username" value="@if($creds) {{ $creds->username }} @endif" />
				</div>
				<div class="col-md-2">
					<label for="password">Password</label>
				</div>
				<div class="col-md-4">
					<input type="password" name="password" id="password" value="@if($creds) {{ $creds->password }} @endif" />
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
