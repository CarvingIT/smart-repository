@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'activePage'=>'Import via URL'])

@section('content')
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: Import via public URL</h4></div>
                <div class="col-md-12 text-right">
                <a href="/collection/{{ $collection->id }}" class="btn btn-sm btn-primary" title="Back"><i class="material-icons">arrow_back</i></a>
                </div>

                <div class="card-body">
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
						<div class="alert alert-<?php echo $msg; ?>">
                        	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        	<i class="material-icons">close</i>
                        	</button>
                        	<span>{{ Session::get('alert-' . $msg) }}</span>
                        </div>
                        @endif
                    @endforeach
                    </div>

<form name="url_import" action="/collection/{{ $collection->id }}/url-import" method="post">
@csrf()
<input type="hidden" name="collection_id" value="{{ $collection->id }}" />
		<div class="form-group row">
	   	   <div class="col-md-3">
		   <label for="title" class="col-md-12 col-form-label text-md-right">Title</label>
		   </div>
           <div class="col-md-9">
               <input class="form-control" type="title" id="title" name="title" size="40"  
               placeholder="If left blank, we shall guess!" />
           </div>
		</div>
		<div class="form-group row">
		   <div class="col-md-3">
		   <label for="url" class="col-md-12 col-form-label text-md-right">URL</label>
		   </div>
   		   <div class="col-md-9">
			<input id="url" type="text" class="form-control" name="url" /> 
   		   </div>
		</div>
	<div class="form-group row mb-0">
		<div class="col-md-9 offset-md-4">
        <button type="submit" class="btn btn-primary"> Save </button>
    	</div>
	</div>
</form>

		@if(count($links) > 0)
		<h4>Links queued for import</h4>
		<div class="col-md-12 row">
			@foreach($links as $l)
				<div class="col-md-3">{{ $l->created_at }}</div>
				<div class="col-md-6">{{ $l->url }}</div>
				<div class="col-md-3"><a class="btn btn-link btn-danger" href="/collection/{{ $collection->id }}/import-link/{{ $l->id }}/delete"><i class="material-icons">delete</i></a></div>
			@endforeach
		</div>	
		@endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
