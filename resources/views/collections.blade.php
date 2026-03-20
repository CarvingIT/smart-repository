@extends('layouts.app',['class' => 'off-canvas-sidebar', 'title' => 'Collections'])

@section('content')
<style>
    .collection-card-sm {
        max-width: 280px;
        margin: 0 auto;
    }

    .collection-cover-frame {
        position: relative;
        width: 100%;
        padding-bottom: 133%;
        border-radius: 8px;
        overflow: hidden;
        background: #2c3e50;
    }

    .collection-cover-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        background: #1f2a36;
    }

    .collection-card-sm .card-header {
        padding: 0.7rem 0.9rem;
    }

    .collection-card-sm .card-body {
        padding: 0.75rem 0.8rem 0.45rem;
    }

    .collection-card-sm .collection-stats-row {
        margin-top: 0.25rem;
        margin-bottom: 0;
    }

    .collection-card-sm .stats-on-card {
        padding-top: 32px;
        padding-bottom: 2px;
        line-height: 1.15;
        font-size: 1rem;
    }

    .collection-card-sm .document-count {
        background-size: 26px;
        background-position: center 4px;
    }

    .collection-card-sm .user-count,
    .collection-card-sm .space-utilization {
        background-size: 22px;
        background-position: center 5px;
    }

    @media (max-width: 576px) {
        .collection-card-sm {
            max-width: 100%;
        }
    }
</style>
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-end mb-3">
        <div class="col-auto">
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="material-icons" style="font-size: 18px; vertical-align: middle;">sort</i>
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
        <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="card collection-card-sm">
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
                    {{-- Representative image tile: portrait 3:4 aspect ratio --}}
                    <a href="/collection/{{ $c->id }}" style="display:block; text-decoration:none;">
                    <div class="mb-2 collection-cover-frame">
                    @if(!empty($c->representative_image))
                        <img src="{{ asset('storage/'.$c->representative_image) }}" alt="{{ $c->name }}" class="collection-cover-image" />
                    @else
                        <div style="position:absolute; top:0; left:0; width:100%; height:100%; background:linear-gradient(160deg, #1a2a3a 0%, #2d4a6b 50%, #1a2a3a 100%); display:flex; flex-direction:column; align-items:center; justify-content:center; padding:16px; box-sizing:border-box;">
                            <div style="border:2px solid rgba(255,255,255,0.4); border-radius:4px; width:80%; padding:20px 12px; text-align:center; background:rgba(255,255,255,0.07);">
                                <div style="font-size:11px; letter-spacing:3px; color:rgba(255,255,255,0.6); text-transform:uppercase; margin-bottom:12px;">Collection</div>
                                <div style="font-size:15px; font-weight:700; color:#fff; line-height:1.3; word-break:break-word;">{{ $c->name }}</div>
                                <div style="width:40px; height:2px; background:rgba(255,255,255,0.4); margin:12px auto;"></div>
                                <div style="font-size:10px; color:rgba(255,255,255,0.5); letter-spacing:1px;">SMART REPOSITORY</div>
                            </div>
                        </div>
                    @endif
                    </div>
                    </a>
                    <div class="row justify-content-center collection-stats-row">
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
