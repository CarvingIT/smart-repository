@extends('layouts.app',['class' => 'off-canvas-sidebar', 'title' => 'Smart Repository'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        @foreach ($collections as $c)
        <div class="col-sm-12 col-md-4">
            <div class="card">
            <div class="card-header card-header-primary">
                  @if ($c->type == 'Members Only')
                    <i class="material-icons">lock</i>
                  @endif
                <span class="card-title"><a href="/collection/{{ $c->id }}" title="Click here to view documents">{{ $c->name }}</a> ({{ count($c->documents) }}) 
            </span>
            </div>
                  <div class="card-body">
                  {{ $c->description }}
                 </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
</div>
@endsection
