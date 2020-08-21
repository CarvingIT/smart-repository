@extends('layouts.app',['class' => 'off-canvas-sidebar','title'=>'Smart Repository','activePage'=>'faq','titlePage'=>'FAQ'])

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
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $c->id }}">{{$c->name}}</a> :: {{ $document->title }}</h4></div>
                <div class="card-body">

                  <div class="row">
                      <div class="col-sm-12 text-right">
                        <a href="javascript:window.history.back();" class="btn btn-sm btn-primary" title="Back">
                        <i class="material-icons">arrow_back</i>
                        </a>
                      </div>
                  </div>
                
                    <div class="row">
                        <div class="col-sm-3 text-right">Title:</div>
                        <div class="col-sm-5">{{ $document->title }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 text-right">Size:</div>
                        <div class="col-sm-5">{{ $document->human_filesize($document->size) }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 text-right">Created by:</div>
                        <div class="col-sm-5">{{ $document->owner->name }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 text-right">Created on:</div>
                        <div class="col-sm-5">{{ $document->updated_at }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 text-right">Type:</div>
                        <div class="col-sm-5">{{ $document->icon($document->path) }}</div>
                    </div>
                    @foreach($document->meta as $m)
                    @if(!empty($meta_labels[$m->meta_field_id]))
                    <div class="row">
                        <div class="col-sm-3 text-right">{{ $meta_labels[$m->meta_field_id] }}:</div>
                        <div class="col-sm-5">{{ $m->value }}</div>
                    </div>
                    @endif
                    @endforeach
                    <div class="row">
                        <div class="col-sm-3 text-right">Download/Open:</div>
                        <div class="col-sm-5"><a href="/document/{{$document->id}}" target="_new"><img class="file-icon" src="/i/file-types/{{ $document->icon($document->path) }}.png"></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
