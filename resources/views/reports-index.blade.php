@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Reports'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">{{ __('Reports') }}</h4></div>
                <div class="card-body">
                  <ul>
                    <li><a href="/reports/downloads">{{ __('Downloads') }}</a></li>
                    <li><a href="/reports/uploads">{{ __('Uploads') }}</a></li>
                    <li><a href="/reports/search-queries">{{ __('Search Queries') }}</a></li>
                    <li><a href="/reports/duplicates">{{ __('Duplicates') }}</a></li>
                  </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
