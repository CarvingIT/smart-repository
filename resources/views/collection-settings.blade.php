@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'activePage'=>'column-config'])

@push('js')
 <link rel="stylesheet" href="/build/assets/css/jquery-ui.min.css">
  <script src="/build/assets/js/jquery-ui.min.js"></script>
  <script>
  $( function() {
	  $( "#accordion" ).accordion({
	  	'collapsible': true,
  		'active':false,
		'heightStyle': "content",
  	});
  } );
  </script>
<script>
$(document).ready(function() {
    $('input[name="use_custom_template"]').on('change', function() {
        if ($(this).val() === '1') {
            $('#template-editors').slideDown();
        } else {
            $('#template-editors').slideUp();
        }
    });
});
</script>
<link href="/build/assets/css/select2.min.css" rel="stylesheet" />
<script src="/build/assets/js/select2.full.min.js"></script>
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
                <div class="card-header card-header-primary"><h4 class="card-title">{{ $collection->name }} :: {{ __('Configuration') }}</h4></div>
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
			@if(!empty($collection->require_approval) && $collection->require_approval == 1)
			<div class="col-md-3"><input name="display_approval_status" type="checkbox" value="1"
            @if(!empty($column_config->display_approval_status) && $column_config->display_approval_status == 1) checked="checked" @endif /> {{ __('Approval Status') }}</div>
			@endif

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
       {{-- <div class="col-md-3"><input name="title_search" type="checkbox" value="1" 
		@if(!empty($column_config->title_search) && $column_config->title_search == 1) checked="checked" @endif /> {{ __('Title') }}</div> --}}
       <div class="col-md-3"><input name="file_type_search" type="checkbox" value="1" 
		@if(!empty($column_config->file_type_search) && $column_config->file_type_search == 1) checked="checked" @endif /> {{ __('File Type') }}</div>
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
			@if(!empty($column_config->auth_user_permissions) && in_array($p->name, $column_config->auth_user_permissions)) checked="checked" @endif /> {{ __($p->description) }}</div>
			@endforeach
		</div>

        <h4>{{ __('Document viewer settings')}}</h4>
		<div class="form-group row">
            <div class="col-md-12"><input name="document_viewer_download" type="checkbox" value="1"
            @if(!empty($column_config->document_viewer_download))
                checked="checked"
            @endif /> {{ __('Allow download of documents') }}</div>
            <div class="col-md-12"><input name="document_viewer_empty_fields" type="checkbox" value="1"
            @if(!empty($column_config->document_viewer_empty_fields))
                checked="checked"
            @endif /> {{ __('Display empty meta information fields') }}</div>
            <div class="col-md-12">
                <strong>{{ __('PDF Viewer:') }}</strong>
                <input type="radio" name="pdf_viewer" value="viewerjs" id="viewer_viewerjs" 
                    @if(@$column_config->pdf_viewer == 'viewerjs' || empty($column_config->pdf_viewer)) checked @endif />
                <label for="viewer_viewerjs">{{ __('Default (ViewerJS)') }}</label>
                <input type="radio" name="pdf_viewer" value="dearflip" id="viewer_dearflip" style="margin-left: 15px;"
                    @if(@$column_config->pdf_viewer == 'dearflip') checked @endif />
                <label for="viewer_dearflip">{{ __('DearFlip (Flip Book)') }}</label>
                <input type="radio" name="pdf_viewer" value="pdfjs" id="viewer_pdfjs" style="margin-left: 15px;"
                    @if(@$column_config->pdf_viewer == 'pdfjs') checked @endif />
                <label for="viewer_pdfjs">{{ __('PDF.js (Modern)') }}</label>
            </div>
			<div class="col-md-12" style="margin-top: 12px;">
				<strong>{{ __('PDF.js Auto-Scroll') }}</strong>
				<div class="row" style="margin-top: 8px;">
					<div class="col-md-3">
						<label for="autoscroll_interval">{{ __('Interval (milliseconds)') }}</label>
						<input type="number" class="form-control" name="autoscroll_interval" id="autoscroll_interval" min="1" step="1" value="{{ old('autoscroll_interval', !empty($column_config->autoscroll_interval) ? $column_config->autoscroll_interval : 50) }}" />
					</div>
					<div class="col-md-3">
						<label for="autoscroll_speed">{{ __('Scroll speed (pixels)') }}</label>
						<input type="number" class="form-control" name="autoscroll_speed" id="autoscroll_speed" min="1" step="1" value="{{ old('autoscroll_speed', !empty($column_config->autoscroll_speed) ? $column_config->autoscroll_speed : 1) }}" />
					</div>
					<div class="col-md-12">
						<small class="form-text text-muted">{{ __('These settings are used only by the PDF.js reader. Lower interval means faster updates; higher speed means more pixels per tick.') }}</small>
					</div>
				</div>
			</div>
        </div>

		<h4>{{ __('Security') }}</h4>
		<div class="form-group row">
			<div class="col-md-12">
				<input name="require_two_factor" id="require_two_factor" type="checkbox" value="1"
				@if(!empty($column_config->require_two_factor) && (int)$column_config->require_two_factor === 1) checked="checked" @endif />
				<label for="require_two_factor">{{ __('Require TOTP (Authy/Authenticator app) verification before accessing this collection') }}</label>
			</div>
			<div class="col-md-12">
				<small class="form-text text-muted">{{ __('When enabled, users must verify with their 2FA code once per session for this collection.') }}</small>
			</div>
		</div>

		<h4>{{__('Document Approval')}}</h4>
		<div class="form-group row">
                  <div class="col-md-12"><input type="checkbox" id="display_unapproved_docs" name="display_unapproved_docs" value="1"
                        @if(@$column_config->display_unapproved_docs == 1) checked @endif />{{ __('Display Unapproved Documents') }}
                  </div>
                    <br />
                  <div class="col-md-12"><input type="checkbox" id="display_approval_log" name="display_approval_log" value="1"
                        @if(@$column_config->display_approval_log == 1) checked @endif />{{ __('Display Document Approval Log') }}
                  </div>
                    <br />
                    <div class="col-md-12">
            <strong>{{ __('Document Work Flow') }}</strong>
			<select class="selectsequence" id="selectsequence" name="approved_by[]" multiple style="width:100%;">	
				@if(!empty($column_config->approved_by))
					@foreach($column_config->approved_by as $approver)
						@foreach($roles as $role)
							@if(!empty($column_config->approved_by) && $approver == $role->id)
								<option value="{{ $role->id }}" @if(!empty($column_config->approved_by) && $role->id == $approver) selected @endif>{{ $role->name }}</option>
							@endif
						@endforeach
			        	@endforeach
				@endif
				@foreach($roles as $role)
					@if (empty($column_config->approved_by) || 
					(!empty($column_config->approved_by) 
					&& !in_array($role->id, $column_config->approved_by)))
					<option value="{{ $role->id }}">{{ $role->name }}</option>
					@endif
				@endforeach
			</select>
            </div>
			<div class="col-md-12" style="margin-top: 12px;">
				<strong>{{ __('Approval Status Labels') }}</strong>
				@php
					$approvalStatusLabels = '';
					if (!empty($column_config->approval_status_labels) && is_array($column_config->approval_status_labels)) {
						$approvalStatusLabels = implode("\n", array_map(function ($label) {
							return is_null($label) ? '' : $label;
						}, $column_config->approval_status_labels));
					}
				@endphp
				<textarea class="form-control" name="approval_status_labels_text" rows="4" placeholder="Submitted&#10;Awaiting HoD's approval&#10;Awaiting final approval">{{ $approvalStatusLabels }}</textarea>
				<small class="form-text text-muted">
					{{ __('Add one status label per line in the same order as the workflow roles above. Leave blank to use default labels.') }}
				</small>
			</div>
			<div class="col-md-12" style="margin-top: 12px;">
				<strong>{{ __('Stage-wise Checklist Labels') }}</strong>
				@php
					$approvalChecklistLabels = '';
					if (!empty($column_config->approval_checklist_labels) && is_array($column_config->approval_checklist_labels)) {
						$approvalChecklistLabels = implode("\n", array_map(function ($stageLabels) {
							if (empty($stageLabels) || !is_array($stageLabels)) {
								return '';
							}

							return implode(' | ', array_map(function ($label) {
								return is_null($label) ? '' : $label;
							}, $stageLabels));
						}, $column_config->approval_checklist_labels));
					}
				@endphp
				<textarea class="form-control" name="approval_checklist_labels_text" rows="5" placeholder="Check document title | Verify attachments\nCheck compliance | Confirm final review">{{ $approvalChecklistLabels }}</textarea>
				<small class="form-text text-muted">
					{{ __('Add one line per workflow stage. Separate checklist labels within a stage using | . The number of lines must match the workflow roles above.') }}
				</small>
			</div>
		</div>

		<h4>{{__('Notifications')}}</h4>
		<div class="form-group row">
			<div class="col-md-2 text-right">
				<label for="slack_webhook"><img src="/i/Slack_Mark_Web.png" class="icon"/>{{ __('Slack Webhook') }}</label>
			</div>
			<div class="col-md-10">
				<input type="text" class="form-control" name="slack_webhook" id="slack_webhook" placeholder="{{ __('Slack webhook url') }}" value="@if(!empty($column_config->slack_webhook)) {{ $column_config->slack_webhook }} @endif" />
			</div>

			<div class="col-md-2 text-right">
				<label for="notify_email"><img src="/i/notify_email.png" class="icon"/>{{ __('Send email to') }}</label>
			</div>
			<div class="col-md-10">
				<input type="text" class="form-control" name="notify_email" id="notify_email" placeholder="{{ __('Notification email address') }}" value="@if(!empty($column_config->notify_email)) {{ $column_config->notify_email }} @endif" />
			</div>
		</div>

		<h4>{{ __('IMAP Settings (Map an email address to this collection)')}}</h4>
		<div class="form-group">
		<p>{{ __('Attachments sent to this email address will be automatically imported into your collection. You may need the help of your IT staff to fill out the following details.') }}</p>
			<div class="row">
				<div class="col-md-2 text-right">
					<label for="email_address">{{ __('Email address') }}</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="email_address" id="email_address" placeholder="{{ __('e.g. knowledge@yourdomain.com') }}" value="@if(!empty($mailbox->address)) {{ $mailbox->address }} @endif" />
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
					<label for="imap_server">{{ __('IMAP server') }}</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="imap_server" id="imap_server" value="@if($creds) {{ $creds->server_address }} @endif" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="server_port">{{ __('Port') }}</label>
				</div>
				<div class="col-md-10">
					<input type="text" class="form-control" name="server_port" id="server_port" value="@if($creds) {{ $creds->server_port }} @endif" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="security">{{ __('SSL/TLS') }}</label>
				</div>
				<div class="col-md-10">
					<select name="security" class="selectpicker" id="security">
						<option value="">{{ __('Security') }}</option>
						<option value="ssl" @if($creds && $creds->security == 'ssl') {{ 'selected' }} @endif>SSL</option>
						<option value="tls" @if($creds && $creds->security == 'tls') {{ 'selected' }} @endif>TLS</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="username">{{ __('Username') }}</label>
				</div>
				<div class="col-md-4">
					<input type="text" name="username" id="username" value="@if($creds) {{ $creds->username }} @endif" />
				</div>
				<div class="col-md-2">
					<label for="password">{{ __('Password') }}</label>
				</div>
				<div class="col-md-4">
					<input type="password" name="password" id="password" value="@if($creds) {{ $creds->password }} @endif" />
				</div>
			</div>
			<div class="row">
			</div>
		</div>


		<h4>{{ __('Display Templates') }}</h4>
		<div class="form-group row">
			<div class="col-md-12" style="margin-bottom:10px;">
				<strong>{{ __('Template Display Mode') }}</strong><br />
				<label style="margin-right:20px;">
					<input type="radio" name="use_custom_template" value="0"
						@if(empty($column_config->use_custom_template) || $column_config->use_custom_template == 0) checked @endif />
					{{ __('Default display') }}
				</label>
				<label>
					<input type="radio" name="use_custom_template" value="1"
						@if(!empty($column_config->use_custom_template) && $column_config->use_custom_template == 1) checked @endif />
					{{ __('Custom template display') }}
				</label>
				<p class="text-muted" style="font-size:0.85em; margin-top:5px;">
					{{ __('Default display applies for new collections. Switch to custom template to control the HTML layout of search results and document detail pages.') }}
				</p>
			</div>

			<div class="col-md-12" id="template-editors" style="@if(empty($column_config->use_custom_template) || $column_config->use_custom_template == 0) display:none; @endif">
				<div class="row" style="margin-bottom:15px;">
					<div class="col-md-12">
						<label><strong>{{ __('Search Result Template') }}</strong></label>
						<p class="text-muted" style="font-size:0.85em;">
							{{ __('HTML template for each row in the search results list.') }}<br />
							{{ __('Available tokens:') }}
					<code>@{{title}}</code>,
					<code>@{{type}}</code>,
					<code>@{{size}}</code>,
					<code>@{{date}}</code>,
					<code>@{{link}}</code>,
					<code>@{{icon_url}}</code>,
					<code>@{{meta_FIELDLABEL}}</code>
						</p>
						<textarea name="search_result_template_html" class="form-control" rows="8"
						placeholder="e.g. &lt;div class=&quot;sr-item&quot;&gt;&lt;a href=&quot;@{{link}}&quot;&gt;&lt;strong&gt;@{{title}}&lt;/strong&gt;&lt;/a&gt;&lt;span class=&quot;meta&quot;&gt;@{{date}}&lt;/span&gt;&lt;/div&gt;">{{ !empty($search_result_template) ? $search_result_template->html_code : '' }}</textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label><strong>{{ __('Details Page Template') }}</strong></label>
						<p class="text-muted" style="font-size:0.85em;">
							{{ __('HTML template for the document details page showing meta information and the download link.') }}<br />
							{{ __('Available tokens:') }}
					<code>@{{title}}</code>,
					<code>@{{type}}</code>,
					<code>@{{size}}</code>,
					<code>@{{date}}</code>,
					<code>@{{link}}</code>,
					<code>@{{icon_url}}</code>,
					<code>@{{meta_FIELDLABEL}}</code>
						</p>
						<textarea name="details_page_template_html" class="form-control" rows="8"
						placeholder="e.g. &lt;div class=&quot;doc-detail&quot;&gt;&lt;h2&gt;@{{title}}&lt;/h2&gt;&lt;p&gt;@{{meta_Description}}&lt;/p&gt;&lt;a href=&quot;@{{link}}&quot;&gt;Download&lt;/a&gt;&lt;/div&gt;">{{ !empty($details_page_template) ? $details_page_template->html_code : '' }}</textarea>
					</div>
				</div>
			</div>
		</div>

		<h4>{{ __('Display of search results') }}</h4>
		<div class="form-group row">
			<div class="col-md-12">
				<input type="checkbox" name="expand_highlights_by_default" id="expand_highlights_by_default" value="1"
					@if(!empty($column_config->expand_highlights_by_default)) checked="checked" @endif />
				<label for="expand_highlights_by_default">{{ __('Expand highlights by default') }}</label>
			</div>
			<div class="form-group row align-items-center" style="width:100%;margin-top:10px;">
				<div class="col-md-3 text-right">
					<label for="fixed_columns_left" class="col-form-label">{{ __('Fixed columns left') }}</label>
				</div>
				<div class="col-md-3">
					<input type="number" class="form-control" name="fixed_columns_left" id="fixed_columns_left" min="0" value="{{ @$column_config->fixed_columns_left ?? 0 }}" />
				</div>
				<div class="col-md-3 text-right">
					<label for="fixed_columns_right" class="col-form-label">{{ __('Fixed columns right') }}</label>
				</div>
				<div class="col-md-3">
					<input type="number" class="form-control" name="fixed_columns_right" id="fixed_columns_right" min="0" value="{{ @$column_config->fixed_columns_right ?? 0 }}" />
				</div>
			</div>
			<div class="col-md-12">
				<input type="checkbox" name="show_word_cloud" id="show_word_cloud" value="1"
					@if(!empty($column_config->show_word_cloud)) checked="checked" @endif />
				<label for="show_word_cloud">{{ __('Show word cloud') }}</label>
			</div>
			<div class="col-md-12">
				<input type="checkbox" name="show_audit_trail" id="show_audit_trail" value="1"
					@if(!empty($column_config->show_audit_trail)) checked="checked" @endif />
				<label for="show_audit_trail">{{ __('Show audit trail') }}</label>
			</div>
		</div>

		</div>
<div class="form-group row mb-0">
    <div class="col-md-9 offset-md-4">
        <button type="submit" class="btn btn-primary"> {{ __('Save') }} </button>
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
