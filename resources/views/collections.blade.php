@extends('layouts.app',['class' => 'off-canvas-sidebar'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @foreach ($collections as $c)
            <div class="card">
            <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collection/{{ $c->id }}" title="Click here to view documents">{{ $c->name }}</a></h4>
                  @if(!empty(Auth::user()->id))
                  @endif
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
