@extends('layouts.app',['class' => 'off-canvas-sidebar', 'title' => 'Collections'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-end mb-3">
        <div class="col-auto">
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="material-icons" style="font-size: 18px; vertical-align: middle;">settings</i> SORT
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="sortDropdown">
                    <a class="dropdown-item {{ (isset($sort_by) && $sort_by == 'name_asc') ? 'active' : '' }}" href="/collections?sort_by=name_asc">
                        <span style="display: inline-block; width: 35px;">A→Z</span> Name (A-Z)
                    </a>
                    <a class="dropdown-item {{ (isset($sort_by) && $sort_by == 'name_desc') ? 'active' : '' }}" href="/collections?sort_by=name_desc">
                        <span style="display: inline-block; width: 35px;">Z→A</span> Name (Z-A)
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item {{ (isset($sort_by) && $sort_by == 'size_desc') ? 'active' : '' }}" href="/collections?sort_by=size_desc">
                        <i class="material-icons" style="font-size: 16px; vertical-align: middle; display: inline-block; width: 35px;">arrow_downward</i> Documents (High to Low)
                    </a>
                    <a class="dropdown-item {{ (isset($sort_by) && $sort_by == 'size_asc') ? 'active' : '' }}" href="/collections?sort_by=size_asc">
                        <i class="material-icons" style="font-size: 16px; vertical-align: middle; display: inline-block; width: 35px;">arrow_upward</i> Documents (Low to High)
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                        {{ (int)@$stats[$c->id]->cnt }}
                    </div>
                    <div class="col-sm-12 col-md-4 text-center stats-on-card user-count">
                    {{ $c->getUsers()->count() }}
                    </div>
                    <div class="col-sm-12 col-md-4 text-center stats-on-card space-utilization">
                    <span>
                    {{ \App\Util::human_filesize((int)@$stats[$c->id]->size) }}
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
