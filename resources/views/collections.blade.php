@extends('layouts.app',['class' => 'off-canvas-sidebar', 'title' => 'Smart Repository'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @foreach ($collections as $c)
            <div class="card">
            <div class="card-header card-header-primary">
                  @if ($c->type == 'Members Only')
                    <i class="material-icons">lock</i>
                  @endif
                <span class="card-title"><a href="/collection/{{ $c->id }}" title="Click here to view documents">{{ $c->name }}</a> 
            </span>
            </div>
                  <div class="card-body">
                  {{ $c->description }}
                 </div>
            </div>
          @endforeach
        </div>
    </div>
</div>
</div>
@endsection
