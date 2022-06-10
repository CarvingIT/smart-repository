@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'activePage'=>'collections'])

@section('content')

<link rel="stylesheet"  href="http://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.min.css" type="text/css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
		@if (empty($collection->id))
                <div class="card-header card-header-primary"><h4 class="card-title">New Child Collection</h4></div>
		@else
                <div class="card-header card-header-primary"><h4 class="card-title">Edit Child Collection</h4></div>
		@endif

                <div class="card-body">
		<div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/admin/collectionmanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      	<i class="material-icons">close</i>
                    	</button>
                    	<span>{{ session('status') }}</span>
                        </div>
                    @endif

                   <form method="post" action="/collection/{{ request()->collection_id }}/save-child-collection">
                    @csrf()
                   <input type="hidden" name="child_collection_id" value="{{ @$collection->id }}" />
                   <input type="hidden" name="collection_id" value="{{ request()->collection_id }}" />
                   <div class="form-group row">
                    <div class="col-md-4">
                   <label for="collection_name" class="col-md-12 col-form-label text-md-right">Name</label> 
                    </div>
                    <div class="col-md-8">
                    <input type="text" name="collection_name" id="collection_name" class="form-control" placeholder="Give your collection a name" value="{{ $collection->name }}" required />
                    </div>
                   </div>
                   <div class="form-group row">
                    <div class="col-md-4">
                   <label for="description" class="col-md-12 col-form-label text-md-right">Description</label> 
			</div>
                    <div class="col-md-8">
                    <textarea name="description" id="description" class="form-control" value="" placeholder="Description" required >{{ $collection->description }}</textarea>
                    </div>
                   </div>
                   <div class="form-group row mb-0"><div class="col-md-8 offset-md-4"><button type="submit" class="btn btn-primary">
                                    Save
                          </button> 
                     </div></div> 
                   </form> 
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
