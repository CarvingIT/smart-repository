@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
            <div class="card-header">{{ $collection->name }}
                  <div class="card-header-corner">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE'))
                    <a href="/collection/{{ $collection->id }}/upload"><img class="icon" src="/i/new-document.png" /></a>
                  @endif
                  </div>
            </div>
                  <div class="card-body">
                    {{ $collection->description }}
                 </div>
            </div>
            <div class="card">
            <div class="card-header">Documents</div>
                 <div class="card-body">
                    {{$documents->links() }}
                    <ul>
                    @foreach($documents as $d)
                    <li><a href="/collection/{{$collection->id}}/document/{{$d->id}}" target="_new">{{ $d->title }}</a></li> 
                    @endforeach
                    </ul>
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection
