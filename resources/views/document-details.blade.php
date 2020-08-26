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
            alert("Edit feature coming soon! You would like to edit the word: " +$(this).text());
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
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $c->id }}">{{$c->name}}</a> :: {{ $document->title }}</h4></div>
                <div class="card-body">

                  <div class="row">
                      <div class="col-md-12 text-right">
                        <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back">
                        <i class="material-icons">arrow_back</i>
                        </a>
                      </div>
                  </div>
                    <div class="row">
                        <div class="col-md-4 text-right">Title:</div>
                        <div class="col-md-8">{{ $document->title }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 text-right">Size:</div>
                        <div class="col-md-8">{{ $document->human_filesize($document->size) }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 text-right">Created by:</div>
                        <div class="col-md-8">{{ $document->owner->name }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 text-right">Created on:</div>
                        <div class="col-md-8">{{ $document->updated_at }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 text-right">Type:</div>
                        <div class="col-md-8">{{ $document->icon($document->path) }}</div>
                    </div>
                    @foreach($document->meta as $m)
                    @if(!empty($meta_labels[$m->meta_field_id]))
                        <div class="row">
                            <div class="col-md-4 text-right">{{ $meta_labels[$m->meta_field_id] }}:</div>
                            <div class="col-md-8">{{ $m->value }}</div>
                        </div>
                    @endif
                    @endforeach
                    </div>
                    <div class="row">
                        <div class="col-md-4 text-right">Download/Open:</div>
                        <div class="col-md-8"><a href="/document/{{$document->id}}" target="_new"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center"><h3>Word cloud</h3></div>
                    </div>
                    <div class="row" style="margin-top:10%;min-height:200px;"> 
                        <div class="col-md-1"></div>
                        <div class="col-md-10"><div id="wordcloud"></div></div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
