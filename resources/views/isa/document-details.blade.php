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
<style>
	.container{
		color:back;
	}
</style>
@endif
@endpush
@section('content')
<div class="container">
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collection/{{ $c->id }}">{{$c->name}}</a> :: Document Details</h4></div>
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
                    <div class="col-md-12">
                        <span id="doc-title" class="col-md-12"><!--h4-->
			@if($c->content_type == 'Uploaded documents')
				@if($document->type == 'application/pdf')
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;<a href="/collection/{{$c->id}}/document/{{$document->id}}" target="_new" style="text-decoration:underline;">{{ $document->title }}</a>
				@elseif($document->type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation')
					<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png" style="float:left;"></a>&nbsp;<a href="/collection/{{ $c->id }}/document/{{ $document->id }}">
				@elseif(preg_match('/^audio/',$document->type) || preg_match('/^video/',$document->type))
					<div>
                        <h6><a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>{{ $document->title }}</h6>
        				
        				</div>
            			<a title="Read online" href="/collection/{{ $document->collection_id }}/document/{{ $document->id }}/media-player" target="_blank">
				@elseif($document->type == 'image/jpeg' || $document->type == 'image/png')
					<div class="col-md-12">
                        <label>{{ $document->title }}</label>
					
					</div>
				@else
				<a href="/collection/{{ $c->id }}/document/{{ $document->id }}"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>&nbsp;<a href="/collection/{{$c->id}}/document/{{$document->id}}" target="_new" style="text-decoration:underline;">{{ $document->title }}</a>
				@endif
			@else
				{{-- --}}
			@endif
                        </div>
				<br />

			@if($c->content_type == 'Uploaded documents')
			<div class="col-md-12">
			<div class="row">
				@foreach($document->collection->meta_fields as $meta_field)

			@php 
				$m = App\MetaFieldValue::where('document_id', $document->id)->where('meta_field_id', $meta_field->id)->first();
				if(!$m || empty($m->value)) continue;
				$meta_field_type = $meta_field->type;
			 @endphp
                        @if(!empty($meta_labels[$m->meta_field_id]))
							@if ($meta_field_type == 'Textarea')
                            <div class="col-md-12">
							@else
                            <div class="col-md-3">
							@endif
                            <label style="margin-top:2em; color: black;" for="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ $meta_labels[$m->meta_field_id] }}</label>
                            <div id="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{!! html_entity_decode($document->meta_value($m->meta_field_id)) !!}</div>
                            </div>
                       	@endif
               @endforeach
			</div>
			@endif

                    </div>
</div>

                   </div><!-- card body ends -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
