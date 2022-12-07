@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'FAQ'])

@php
    $c = \App\Collection::find($document->collection_id);
    $meta_fields = $c->meta_fields;
    $meta_labels = array();
    foreach($meta_fields as $mf){
        $meta_labels[$mf->id] = $mf->label;
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

                  <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                        <span id="doc-title" class="col-md-12"><!--h4-->
			@if($c->content_type == 'Uploaded documents')
				@if($document->type == 'application/pdf')
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;
            				<a title="Read online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}/pdf-reader" target="_blank">
				@elseif($document->type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation')
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;<a href="/collection/{{ $c->id }}/document/{{ $document->id }}">
				@elseif(preg_match('/^audio/',$document->type) || preg_match('/^video/',$document->type))
					<div style="text-align:center;">
                        		<h3><a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>{{ $document->title }}</h3>
        				<video controls >
        				<source src="/collection/{{ $c->id }}/document/{{ $document->id }}" type="video/mp4">
        				</video>
        				</div>
            			<a title="Read online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}/media-player" target="_blank">
				@elseif($document->type == 'image/jpeg' || $document->type == 'image/png')
					<div style="text-align:center;">
                        		<h3><a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>{{ $document->title }}</h3>
					<img src="/collection/{{ $c->id }}/document/{{ $document->id }}" style="width:50%">
					</div>
            			<a title="Read online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}" target="_blank">
				@else
				<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;<a href="/collection/{{$c->id}}/document/{{$document->id}}" target="_new" style="text-decoration:underline;">
				@endif
			@else
			<a href="{{ $document->url }}" target="_new" style="text-decoration:underline;">
			@endif
			</span>{{-- don't need this span --}}
                        </div>

			@if($c->content_type == 'Uploaded documents')
			<div class="row">
				@foreach($document->collection->meta_fields as $meta_field)

			@php 
				$m = App\MetaFieldValue::where('document_id', $document->id)->where('meta_field_id', $meta_field->id)->first();
				if(!$m) continue;
				$meta_field_type = $meta_field->type;
			 @endphp
                        @if(!empty($meta_labels[$m->meta_field_id]))
							@if ($meta_field_type == 'Textarea')
                            <div class="col-md-12">
							@else
                            <div class="col-md-3">
							@endif
                            <label for="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ $meta_labels[$m->meta_field_id] }}</label>
							@if($m->meta_field->type == 'MultiSelect' || $m->meta_field->type == 'Select')
                            <span id="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ @implode(", ",json_decode($m->value)) }}</span>
							@else
                            <!--span id="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ $m->value }}</span-->
                            <div id="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{!! $m->value !!}</div>
				<br />
							@endif
                            </div>
                        @endif
                        @endforeach
			</div><!-- row ends -->
			@endif
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
										$what_changed = $mfv->meta_field->label;
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


<!--
                    <div class="col-md-3">
                        <div class="col-md-12">
                        <span id="doc-download-open" class="col-md-12">
			@if($c->content_type == 'Uploaded documents')
			<a title="Download" href="/collection/{{$c->id}}/document/{{$document->id}}" target="_new" style="text-decoration:underline;">
			@else
			<a href="{{ $document->url }}" target="_new" style="text-decoration:underline;">
			@endif
                        <img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>
                        </span>
			@if(Auth::user() && (Auth::user()->hasPermission($collection->id, 'MAINTAINER') || Auth::user()->hasPermission($collection->id, 'EDIT_ANY')))
					@if(env('ENABLE_PROOFREAD') == 1)
                        <span id="doc-proofread" class="col-md-12">
			<a title="Proofread" href="/collection/{{$c->id}}/document/{{$document->id}}/proofread"><img class="file-icon" src="/i/proofread.png" /></a>
			</span>
					@endif
			@endif
                        </div>
                        <div class="col-md-12">
                        <label for="doc-size" class="col-md-12">Size</label>
                        <span id="doc-size" class="col-md-12">{{ $document->human_filesize($document->size) }}</span>
                        </div>
			@if($c->content_type == 'Uploaded documents')
                        <div class="col-md-12">
                        <label for="doc-creator" class="col-md-12">Created by</label>
                        <span id="doc-creator" class="col-md-12">{{ $document->owner->name }}</span>
                        </div>
			@endif
                        <div class="col-md-12">
                        <label for="doc-updated" class="col-md-12">Updated</label>
                        <span id="doc-updated" class="col-md-12">{{ $document->updated_at }}</span>
                        </div>
                        <div class="col-md-12">
                        <label for="doc-type" class="col-md-12">Type</label>
                        <span id="doc-type" class="col-md-12">{{ $document->type }}</span>
                        </div>
                    </div>
                  </div>
-->

                   </div><!-- card body ends -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
