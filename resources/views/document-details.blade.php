@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'FAQ'])

@php
    $c = \App\Collection::find($document->collection_id);
    $meta_fields = $c->meta_fields;
    $meta_labels = array();
    foreach($meta_fields as $mf){
        $meta_labels[$mf->id] = @$mf->label;
    }
	$col_config = json_decode($c->column_config);
@endphp
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

function randomString(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

function showDeleteDialog(){
    str = randomString(6);
    $('#text_captcha').text(str);
    $('#hidden_captcha').val(str);
        deldialog = $( "#deletedialog" ).dialog({
        title: 'Are you sure ?',
        resizable: true
        });
}
</script>
@if (!empty($col_config->show_word_cloud))
<script src="/js/jQWCloudv3.4.1.js"></script>
<script>
var docwords = new Array();
@foreach($word_weights as $word=>$weight)
    docwords.push({word:'{{ $word }}', weight:{{ $weight }} });
@endforeach
$(document).ready(function()
{
    $("#wordcloud").jQWCloud({
        words:docwords, 
        //cloud_color: 'yellow',        
        minFont: 10,
        maxFont: 50,
        //fontOffset: 5,
        //cloud_font_family: 'Owned',
        //verticalEnabled: false,
        padding_left: 1,
        //showSpaceDIV: true,
        //spaceDIVColor: 'white',
        word_common_classes: 'WordClass',
        word_mouseEnter :function(){
            $(this).css("text-decoration","underline");
        },
        word_mouseOut :function(){
            $(this).css("text-decoration","none");
        },
        word_click: function(){
            //alert("Edit feature coming soon! You would like to edit the word: " +$(this).text());
            alert("Feature coming soon!");
        },
        beforeCloudRender: function(){
               date1=new Date();
        },
        afterCloudRender: function(){
                var date2=new Date();
                console.log("Cloud Completed in "+(date2.getTime()-date1.getTime()) +" milliseconds");
            }
    });
});
</script>
@endif
@endpush
@section('content')
<div class="container">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">{{ __('Collections') }}</a> :: <a href="/collection/{{ $c->id }}">{{$c->name}}</a> :: Document Details</h4></div>
                <div class="card-body">

                  <div class="row">
                      <div class="col-md-12 text-right">
                        <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back">
                        <i class="material-icons">arrow_back</i>
                        </a>
                      </div>
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


                  <div class="row">
                    <div class="col-md-12" style="margin-bottom:5%;">
                        <span id="doc-title" class="col-md-12"><!--h4-->
			@if($c->content_type == 'Uploaded documents')
				@if($document->type == 'application/pdf')
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}/pdf-reader" target="_new"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;
            				<a title="Read online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}/pdf-reader" target="_new">
				@elseif($document->type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation')
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;<a href="/collection/{{ $c->id }}/document/{{ $document->id }}">
				@elseif(preg_match('/^audio/',$document->type) || preg_match('/^video/',$document->type))
					<div style="text-align:center;">
                        		<h3><a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>{{ $document->title }}</h3>
        				<video controls >
        				<source src="/collection/{{ $c->id }}/document/{{ $document->id }}" type="video/mp4">
        				</video>
        				</div>
            			<a title="See online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}/media-player" target="_blank">
				@elseif($document->type == 'image/jpeg' || $document->type == 'image/png')
					<div style="text-align:center;">
                        		<h3><a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>{{ $document->title }}</h3>
					<img src="/collection/{{ $c->id }}/document/{{ $document->id }}" style="width:50%">
					</div>
            			<a title="View" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}" target="_blank">
				@else
				@if ($document->path != 'N/A')
				<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;<a href="/collection/{{$c->id}}/document/{{$document->id}}" target="_new" style="text-decoration:underline;">
				@else
				<img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;" />
				@endif
				@endif
			@else
			<a href="{{ $document->url }}" target="_new" style="text-decoration:underline;">
			@endif
			{!! strip_tags($document->title) !!}
			</a>
			</span>{{-- don't need this span --}}
                        </div>
				<br />

			@if($c->content_type == 'Uploaded documents')
			<div class="col-md-12 row">
				@foreach($document->collection->meta_fields as $meta_field)

			@php 
				$m = App\MetaFieldValue::where('document_id', $document->id)->where('meta_field_id', $meta_field->id)->first();
				if(!$m) continue;
				$meta_field_type = $meta_field->type;
			 @endphp
                        @if(!empty($meta_labels[$m->meta_field_id]))

							@php
								$extra_attributes = empty($m->meta_field->extra_attributes) ? null : json_decode($m->meta_field->extra_attributes);
								$w = empty($extra_attributes->width_on_info_page)? 12 : $extra_attributes->width_on_info_page;
								$show_on_details_page = empty($extra_attributes->show_on_details_page)? '' : $extra_attributes->show_on_details_page;
				if($show_on_details_page == 0) continue;
							@endphp
                            <div class="col-md-{{ $w }}">

                            <label for="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ $meta_labels[$m->meta_field_id] }}</label>
							@if($m->meta_field->type == 'MultiSelect' || $m->meta_field->type == 'Select')
                            <span id="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ @implode(", ",json_decode($m->value)) }}</span>
							@elseif ($m->meta_field->type ==  'TaxonomyTree')
                            <span id="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ $document->meta_value($m->meta_field_id) }}</span>
							@else
                            <div id="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{!! html_entity_decode($m->value) !!}</div>
							@endif
                            </div>
				<br />
                        @endif
                        @endforeach

			@if(\Auth::user() && ($c->require_approval == 1))
                  	<div class="col-md-12">
				<h3>Document Status</h3>
				@if(!empty($document->approved_on))
				@endif

				@if(\Auth::user()->hasPermission($document->collection_id ,'APPROVE'))
				<form name="doc_approve" method="post" action="/approve-document">
				@csrf		
				<input type="hidden" name="collection_id" value="{{ $document->collection_id }}">
				<input type="hidden" name="document_id" value="{{ $document->id }}">
				<br />
					@if(!empty($document->approved_on))
						<h4>Document is Approved</h4> 
						<input id="approved_on" type="hidden" name="approved_on" value=""/>
						<button type="submit" class="btn btn-primary">Disapprove Document</button>
					@else
						<h4>Document is Disapproved</h4> 
						<input id="approved_on" type="hidden" name="approved_on" value="1"/>
						<button type="submit" class="btn btn-primary">Approve Document</button>
					@endif
				</form>
				@endif {{-- has approve permission --}}
			</div>
				@if(empty($document->approved_on))
                  	<div class="col-md-12">
                   		<h3>Comments</h3>
				@foreach($comments as $comment)
				<div>{{ $comment->user->name }}({{ $comment->user->email }}) said: <p>{{ $comment->comment }}</p></div>
				@endforeach	
			</div>
                  	<div class="col-md-12">
				<form name="comment" action="/save-comment" method="post">
				@csrf
				<input type="hidden" name="collection_id" value="{{ $document->collection_id }}">
				<input type="hidden" name="document_id" value="{{ $document->id }}">
                   			<h3>Leave a Comment</h3>
                    			<div id="doc_meta_description" class="col-md-12"><textarea name="comment" id="comment" class="form-control" cols="80" rows="10" value="" placeholder="Enter your comment here" required ></textarea></div>
					<button type="submit" class="btn btn-primary"> Add Comment </button>
				</form>	
                        </div>
				@endif {{-- display comments only for unapproved documents --}}
			@endif {{-- display document status and comment section only for logged in user --}}

			</div><!-- row ends -->
			@endif

        <div id="deletedialog" style="display:none;">
        <form name="deletedoc" method="post" action="/document/delete">
        @csrf
        <p>Enter <span id="text_captcha"></span> to delete</p>
        <input type="text" name="delete_captcha" value="" />
        <input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
        <input type="hidden" id="delete_doc_id" name="document_id" value="{{ $document->id }}" />
        <button class="btn btn-danger" type="submit" value="delete">Delete</button>
        </form>
        </div>

                  <div class="row">
                      <div class="col-md-12">
						@if (Auth::user() && Auth::user()->canEditDocument($document->id))
                        <a href="/document/{{ $document->id }}/edit" class="btn btn-sm btn-primary" title="Edit">
                        <i class="material-icons">edit</i>
                        </a>
						@endif
						@if (Auth::user() && Auth::user()->canDeleteDocument($document->id))
                        <a href="javascript:return false;" onclick="showDeleteDialog();" class="btn btn-sm btn-primary" title="Delete">
                        <i class="material-icons">delete</i>
                        </a>
						@endif
                      </div>
                  </div>
						@if (!empty($col_config->show_word_cloud))
                        <div class="col-md-12"><div id="wordcloud"><img src='/i/processing.gif'></div></div>
						@endif

						@if (!empty($col_config->show_audit_trail))
						<div class="col-md-12">
						<h3>Audit Trail</h3>
						@php
						$all_audits = [];
						foreach($document->audits as $a){
							$audit_metadata = $a->getMetadata();
							$all_audits[$audit_metadata['audit_created_at']][] = $a;
						}

						foreach($document->meta as $m){
							foreach($m->audits as $ma){
								$audit_metadata = $ma->getMetadata();
								$all_audits[$audit_metadata['audit_created_at']][] = $ma;
							}
						}
						// ordering of audits
						krsort($all_audits);
						@endphp

						@if(count($all_audits) == 0)
							<p>No changes have taken place to the meta information of this document.</p>
						@else
						<div class="col-md-12" id="accordion">
						@foreach($all_audits as $k=>$va)
						<h3>{{ $k }}</h3>
						<div>
						@foreach($va as $v)
							@php
								$audit_meta = $v->getMetadata();
								$modified = $v->getModified();
								$model_type = $v->auditable_type;
								$model_id = $v->auditable_id;
							@endphp
								<p>Audit event: {{ @$audit_meta['audit_event'] }}</p>
								<p>User: {{ @$audit_meta['user_name'] }}</p>
								<p>User agent: {{ @$audit_meta['audit_user_agent'] }}</p>
								<p>URL: {{ @$audit_meta['audit_url'] }}</p>
								<p>IP Address: {{ @$audit_meta['audit_ip_address'] }}</p>
								<h4>Modifications</h4>
								@foreach($modified as $mk => $mv)
									@php
									if($mk == 'meta_field_id' || $mk == 'id') continue;
									$what_changed = $mk;
									if($model_type == 'App\MetaFieldValue'){
										$mfv = App\MetaFieldValue::find($model_id);
										$what_changed = @$mfv->meta_field->label;
									}
									@endphp
								<p>
								<em class="audit-changes">{{ $what_changed }}</em>
									was updated from
									<em class="audit-changes">@if(!empty($mv['old'])) {{ $mv['old'] }} @else {{ 'NULL' }} @endif </em>
									to
								<em class="audit-changes">@if(!empty($mv['new'])) {{ $mv['new'] }} @else {{ 'NULL' }} @endif</em>.
								</p>
								<hr />
								@endforeach	
						@endforeach
						</div>
						@endforeach
						</div><!-- accordion ends -->
						@endif

						</div>
						@endif

						@if(Auth::user() && Auth::user()->hasPermission($document->collection->id, 'MAINTAINER'))
						@if ($document->collection->parent_id || $document->collection->children->count() > 0)
							<div class="col-md-12" id="accordion">
								<h3>Actions</h3>
								<form method="post" action="/collection/move_document">
								@csrf
								<input type="hidden" name="document_id" value="{{ $document->id }}" />
								<div class="row">
								<div class="col-md-9">
									<select class="selectpicker" name="collection_id">
										<option value="">Move to - </option>
										@foreach ($document->collection->children as $c)
										<option value="{{ $c->id }}">{{ $c->name }}</option>
										@endforeach
										@if ($document->collection->parent_id)
										<option value="{{ $document->collection->parent_id }}">{{$document->collection->parent->name}}</option>
										@endif
									</select>
								</div>
								<div class="col-md-3">
									<button type="submit" class="btn btn-primary"> Move </button>
								</div>
								</div>
								</form>
							</div>	
						@endif
						@endif


                    </div>

                   </div><!-- card body ends -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
