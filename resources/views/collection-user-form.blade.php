@extends('layouts.app')

@section('content')

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript">
//var path = "{{ route('autocomplete') }}";
//   $('#user_id').typeahead({
 //     source:  function (query, process) {
  //      return $.get(path, { query: query }, function (data) {
   //             return process(data);
    //        });
     //   }
   // });


$(document).ready(function(){

 $('#user_id').keyup(function(){ 
        var query = $(this).val();
        if(query != '')
        {
         var _token = $('input[name="_token"]').val();
         $.ajax({
          url:"{{ route('autocomplete') }}",
          method:"GET",
          data:{query:query, _token:_token},
          success:function(data){
           $('#countryList').fadeIn();  
                    $('#countryList').html(data);
          }
         });
        }
    });

    $(document).on('click', 'li', function(){  
        $('#user_id').val($(this).text());  
        $('#countryList').fadeOut();  
    });  

});
</script>


<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary"><h4 class="card-title"><a href="/collections">Collections</a> :: <a href="/collection/{{ $collection->id }}">{{ $collection->name }}</a> :: User Permissions</h4></div>

                <div class="card-body">
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
                    <input type="text" name="user_id" id="user_id" class="typeahead form-control" placeholder="User Email ID" 
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
