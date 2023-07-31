@extends('layouts.app',['class' => 'off-canvas-sidebar', 'title' => 'Smart Repository'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        @foreach ($collections as $c)
		@if($c->content_type == 'Web resources' && env('SHOW_WEB_RESOURCES') != 1)
			@continue
		@endif
        <div class="col-sm-12 col-md-4">
            <div class="card">
            <div class="card-header card-header-primary">
                  @if ($c->type == 'Members Only')
                    <i class="material-icons">lock</i>
                  @endif
		<span class="card-title"><a href="/collection/{{ $c->id }}" title="Click here to view documents">{{ $c->name }}</a>
@if (env('ENABLE_COLLECTION_COUNT') == 1) 
({{ $c->documents->count() }}) 
@endif 
            </span>
            </div>
                  <div class="card-body" style="height:100px; overflow:scroll;">
                  {{ $c->description }}
                 </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
</div>
@endsection
