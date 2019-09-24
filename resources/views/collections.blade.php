@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach ($collections as $c)
            <div class="card">
            <div class="card-header">{{ $c->name }}</div>
                  <div class="card-body">
                  Description of the collection 
                 </div>
            </div>
          @endforeach
        </div>
    </div>
</div>
@endsection
