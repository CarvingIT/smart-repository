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
		@if (empty($disk->id))
                <div class="card-header card-header-primary"><h4 class="card-title">New Disk</h4></div>
		@else
                <div class="card-header card-header-primary"><h4 class="card-title">Edit Disk</h4></div>
		@endif

                <div class="card-body">
		<div class="row">
                  <div class="col-md-12 text-right">
                      <a href="/admin/diskmanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
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

                   <form method="post" action="/admin/savedisk">
                    @csrf()
                    <input type="hidden" name="disk_id" value="{{$disk->id}}" />

                   <div class="form-group row">
                    <div class="col-md-3">
                   <label for="disk_name" class="col-md-12 col-form-label text-md-right">Name</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="disk_name" id="disk_name" class="form-control" placeholder="Give your disk a unique name. Avoid spaces." value="{{ $disk->name }}" required />
                    </div>
                   </div>

                   <div class="form-group row">
                    <div class="col-md-3">
                   <label for="driver" class="col-md-12 col-form-label text-md-right">Type/Driver</label> 
                    </div>
                    <div class="col-md-9">
					<select name="driver" class="selectpicker">
						<option value="ftp">FTP</option>
						<option value="sftp">SFTP</option>
						<option value="s3">S3</option>
					</select>
                    </div>
                   </div>

				  <div id="ftp_details">

                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="host" class="col-md-12 col-form-label text-md-right">Host</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="host" id="host" class="form-control" placeholder="Server address" value="{{ $disk->host }}"/>
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="port" class="col-md-12 col-form-label text-md-right">Port</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="port" id="port" class="form-control" placeholder="Port" value="{{ $disk->port }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="username" class="col-md-12 col-form-label text-md-right">Username</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="{{ $disk->username }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="password" class="col-md-12 col-form-label text-md-right">Password</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" value="{{ $disk->password }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="root" class="col-md-12 col-form-label text-md-right">Root</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="root" id="root" class="form-control" placeholder="Path of the root directory on the server" value="{{ $disk->root }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="timeout" class="col-md-12 col-form-label text-md-right">Timeout</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="timeout" id="timeout" class="form-control" placeholder="Timeout in seconds" value="{{ $disk->timeout }}" />
                    </div>
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
