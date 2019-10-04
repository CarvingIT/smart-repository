@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach ($collections as $c)
            <div class="card">
            <div class="card-header"><a href="/collection/{{ $c->id }}">{{ $c->name }}</a>
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
@endsection
