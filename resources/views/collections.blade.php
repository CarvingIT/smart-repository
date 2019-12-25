@extends('layouts.app')

@section('content')
<div class="container" style="height:auto; margin-top:5%;">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @foreach ($collections as $c)
            <div class="card">
            <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collection/{{ $c->id }}">{{ $c->name }}</a></h4>
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
