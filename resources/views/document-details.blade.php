@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'FAQ'])

@push('js')
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
@endpush
@section('content')
<div class="container">
<div class="container-fluid">

@php
    $c = \App\Collection::find($document->collection_id);
    $meta_fields = $c->meta_fields;
    $meta_labels = array();
    foreach($meta_fields as $mf){
        $meta_labels[$mf->id] = $mf->label;
    }
@endphp
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $c->id }}">{{$c->name}}</a> :: Document Details</h4></div>
                <div class="card-body">

                  <div class="row">
                      <div class="col-md-12 text-right">
                        <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back">
                        <i class="material-icons">arrow_back</i>
                        </a>
                      </div>
                  </div>

                  <div class="row">
                    <div class="col-md-9">
                        <div class="col-md-12">
                        <span id="doc-title" class="col-md-12"><h4>
			@if($c->content_type == 'Uploaded documents')
			<a href="/collection/{{$c->id}}/document/{{$document->id}}" target="_new" style="text-decoration:underline;">
			@else
			<a href="{{ $document->url }}" target="_new" style="text-decoration:underline;">
			@endif
			{{ $document->title }}</a></h4></span>
                        </div>
                        <div class="col-md-12"><div id="wordcloud"><img src='/i/processing.gif'></div></div>
                    </div>
                    <div class="col-md-3">
                        <div class="col-md-12">
                        <span id="doc-download-open" class="col-md-12">
			@if($c->content_type == 'Uploaded documents')
			<a href="/collection/{{$c->id}}/document/{{$document->id}}" target="_new" style="text-decoration:underline;">
			@else
			<a href="{{ $document->url }}" target="_new" style="text-decoration:underline;">
			@endif
                        <img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a>
                        </span>
                        <span id="doc-proofread" class="col-md-12">
			<a href="/collection/{{$c->id}}/document/{{$document->id}}/proofread"><img class="file-icon" src="/i/proofread.png" /></a>
			</span>
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
			@if($c->content_type == 'Uploaded documents')
                        @foreach($document->meta as $m)
                        @if(!empty($meta_labels[$m->meta_field_id]))
                            <div class="col-md-12">
                            <label for="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ $meta_labels[$m->meta_field_id] }}</label>
                            <span id="doc-meta-{{ $meta_labels[$m->meta_field_id] }}" class="col-md-12">{{ $m->value }}</span>
                            </div>
                        @endif
                        @endforeach
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
