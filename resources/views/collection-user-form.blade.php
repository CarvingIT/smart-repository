@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/js/jquery-3.3.1.js"></script>
<link rel="stylesheet"  href="/css/jquery-ui.css" type="text/css"> 
<script src="/js/jquery-ui.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	//alert("js is working");
        src = "{{ route('autocomplete') }}";
        $( "#user_id" ).autocomplete({
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
</script>
@endpush

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: User Permissions</h4></div>

                <div class="card-body">
		<div class="row">
			<div class="col-md-12 text-right">
                      	<a href="/collection/{{ $collection->id }}/users" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
                  </div>
                </div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
				<div class="alert alert-success">
                    		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      		<i class="material-icons">close</i>
                    		</button>
                    		<span>{{ session('status') }}</span>
                        </div>
                    @endif

                   <form method="post" action="/collection/{{ $collection->id }}/savecollectionuser">
                    @csrf()
                    <input type="hidden" name="collection_id" value="{{$collection->id}}" />
                   <div class="form-group row">
			<div class="col-md-4">
                   <label for="user_id" class="col-md-10 col-form-label text-md-right">User ID</label> 
			</div>
                    <div class="col-md-4">
                    <input type="text" name="user_id" id="user_id" class="form-control" placeholder="User Email ID" 
                    value="@if(!empty($user->id)){{ $user->email }}@endif" required/>
			<div id="countryList"></div>
                    </div>
                   </div>
                    @foreach(\App\Permission::all() as $p)
			@if(count($collection_has_approval)==0 && $p->name == 'APPROVE')
			@else
                   <div class="form-group row">
                   <label for="permission" class="col-md-4 col-form-label text-md-right"></label> 
                    <div class="col-md-6">
                    <input type="checkbox"  name="permission[]" value="{{ $p->id }}" 
                    @if(!empty($user_permissions['p'.$p->id]))
                     checked 
                    @endif
                    />  {{ $p->name }}
                    </div>
                   </div>
			@endif
                    @endforeach
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
</div>
@endsection
