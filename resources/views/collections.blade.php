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
		<span class="card-title"><a href="/collection/{{ $c->id }}" title="{{ $c->description }}">{{ $c->name }}</a>
@if (env('ENABLE_COLLECTION_COUNT') == 1) 
({{ $c->documents->count() }}) 
@endif 
            </span>
            </div>
                  <div class="card-body">
                    <div class="row justify-content-center">
                    <div class="col-sm-12 col-md-4 text-center stats-on-card document-count">
                        {{ $c->documents->count() }}
                    </div>
                    <div class="col-sm-12 col-md-4 text-center stats-on-card user-count">
                    {{ $c->getUsers()->count() }}
                    </div>
                    <div class="col-sm-12 col-md-4 text-center stats-on-card space-utilization">
                    <span>
                    {{ \App\Util::human_filesize($c->documents->sum('size')) }}
                    </span>
                    </div>
                    </div>
                 </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
</div>
@endsection
