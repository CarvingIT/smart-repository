@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach ($collections as $c)
            <div class="card">
            <div class="card-header">{{ $c->name }}
                  @if(!empty(Auth::user()->id))
                  <div style="float:right;"><a href="/collection/{{ $c->id }}/upload">Add</a></div></div>
                  @endif
                  <div class="card-body">
                  Description of the collection 
                 </div>
            </div>
          @endforeach
        </div>
    </div>
</div>
@endsection
