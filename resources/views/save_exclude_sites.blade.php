@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')

<link rel="stylesheet"  href="http://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.min.css" type="text/css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
@push('js')
<script type="text/javascript">
$(document).ready(function() {
        //alert("js is working");
        src = "{{ route('autocomplete') }}";
        $( "#maintainer" ).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: src,
                    method: 'GET',
                    dataType: "json",
                    data: {
                        term : request.term
                    },
                    success: function(data) {
                        //console.log(data);
                        response(data);
                    }
                });
            },
            minLength: 1,
        });
    });

function hideStorageDriveField(){
var e = document.getElementById("content_type");
var content_type = e.options[e.selectedIndex].value;
//alert(content_type);
if(content_type=='Web resources'){
	document.getElementById("storage_drive").style.display = 'none';
}
else{
	document.getElementById("storage_drive").style.display = 'block';
}
}

</script>
@endpush

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title">Add/Exclude Collection Urls</h4></div>

                <div class="card-body">
		<div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/admin/collectionmanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>
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
	
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      	<i class="material-icons">close</i>
                    	</button>
                    	<span>{{ session('status') }}</span>
                        </div>
                    @endif

                   <form method="post" action="/collection/{{$collection->id}}/savecollectionurls">
                    @csrf()
                    <input type="hidden" name="collection_id" value="{{$collection->id}}" />
                   <div class="form-group row">
                    <div class="col-md-4">
                   <label for="spidered_domain" class="col-md-12 col-form-label text-md-right">Spidered Domains</label> 
                    </div>
                    <div class="col-md-8">
					@foreach($spidered_domains as $d)
						<div><a href="/collection/{{ $collection->id }}/remove-spidered-domain/{{ $d->id }}"><i class="material-icons">delete</i></a>{{ $d->web_address }}</div>
					@endforeach
                    <input type="text" name="spidered_domain" id="spidered_domain" class="form-control" placeholder="http://domain.com" value="" />
                    </div>
                   </div>
                   <div class="form-group row">
                    <div class="col-md-4">
                   <label for="save_urls" class="col-md-12 col-form-label text-md-right">Desired Links</label> 
                    </div>
                    <div class="col-md-8">
					@foreach($desired as $d)
						<div><a href="/collection/{{ $collection->id }}/remove-desired-link/{{ $d->id }}"><i class="material-icons">delete</i></a>{{ $d->url_start_pattern }}</div>
					@endforeach
                    <textarea name="save_urls" id="save_urls" class="form-control" placeholder="http://domain.com/sub_directory1
http://domain.com/sub_directory2" value="" /></textarea>
                    </div>
                   </div>
                   <div class="form-group row">
                     <div class="col-md-4">
                     <label for="exclude_urls" class="col-md-12 col-form-label text-md-right">Excluded Links</label> 
		     </div>
                    <div class="col-md-8">
					@foreach($excluded as $d)
						<div><a href="/collection/{{ $collection->id }}/remove-excluded-link/{{ $d->id }}"><i class="material-icons">delete</i></a>{{ $d->url_start_pattern }}</div>
					@endforeach
                    <textarea name="exclude_urls" id="exclude_urls" class="form-control" value="" 
			placeholder="http://domain.com/sub_directory1
http://domain.com/sub_directory2"></textarea>
                    </div>
                   </div>
                   <div class="form-group row mb-0"><div class="col-md-8 offset-md-4"><button type="submit" class="btn btn-primary">
                                    Add
                          </button> 
                     </div></div> 
                   </form> 
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
