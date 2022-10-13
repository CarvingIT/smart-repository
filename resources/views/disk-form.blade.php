@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')

<link rel="stylesheet"  href="http://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.min.css" type="text/css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
@push('js')
<script type="text/javascript">
$(document).ready(function(){
	var driver = $('#driver').val();
	showDriverFields(driver);
});
function showDriveFields(drive){
	if(drive == 'ftp' || drive == 'sftp'){
		$('#ftp_details').show();	
		$('#s3_details').hide();	
		$('#google_drive_details').hide();
	}
	else if (drive == 's3'){
		$('#ftp_details').hide();	
		$('#s3_details').show();	
		$('#google_drive_details').hide();
	}
	else if(drive == 'google'){
		$('#s3_details').hide();	
		$('#ftp_details').hide();	
		$('#google_drive_details').show();
	}
	else{}
}

$(document).ready(function(){
	showDriveFields($('#driver').val());
}
);
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
                      <a href="/admin/storagemanagement" class="btn btn-sm btn-primary" title="Back to List"><i class="material-icons">arrow_back</i></a>
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
                    <input type="text" name="disk_name" id="disk_name" class="form-control" placeholder="Only alphabets and numbers; no spaces or special characters." value="{{ $disk->name }}" required />
                    </div>
                   </div>

                   <div class="form-group row">
                    <div class="col-md-3">
                   <label for="driver" class="col-md-12 col-form-label text-md-right">Type/Driver</label> 
                    </div>
                    <div class="col-md-9">
					<select name="driver" class="selectpicker" id="driver" onchange="showDriveFields(this.value);">
						<option value="">Drive Type</option>
						<option value="google" @if ($disk->driver == 'google') selected @endif>Google Drive</option>
						<option value="ftp" @if ($disk->driver == 'ftp') selected @endif>FTP</option>
						<option value="sftp" @if ($disk->driver == 'sftp') selected @endif>SFTP</option>
						<option value="s3" @if ($disk->driver == 's3') selected @endif>S3</option>
					</select>
                    </div>
                   </div>
					@php
						$disk_config = json_decode($disk->config);
					@endphp

				  <div id="ftp_details" style="display:none;">

                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="host" class="col-md-12 col-form-label text-md-right">Host</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="host" id="host" class="form-control" placeholder="Server address" value="{{ @$disk_config->host }}"/>
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="port" class="col-md-12 col-form-label text-md-right">Port</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="port" id="port" class="form-control" placeholder="Port" value="{{ @$disk_config->port }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="username" class="col-md-12 col-form-label text-md-right">Username</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="{{ @$disk_config->username }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="password" class="col-md-12 col-form-label text-md-right">Password</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" value="{{ @$disk_config->password }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="root" class="col-md-12 col-form-label text-md-right">Root</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="root" id="root" class="form-control" placeholder="Path of the root directory on the server" value="{{ @$disk_config->root }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="timeout" class="col-md-12 col-form-label text-md-right">Timeout</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="timeout" id="timeout" class="form-control" placeholder="Timeout in seconds" value="{{ @$disk_config->timeout }}" />
                    </div>
                   </div>

				  </div>

				  <div id="s3_details" style="display:none;">

                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="key" class="col-md-12 col-form-label text-md-right">Key</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="key" id="key" class="form-control" placeholder="S3 key" value="{{ @$disk_config->key }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="secret" class="col-md-12 col-form-label text-md-right">Secret</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="secret" id="secret" class="form-control" placeholder="S3 secret" value="{{ @$disk_config->secret }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="region" class="col-md-12 col-form-label text-md-right">Region</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="region" id="region" class="form-control" placeholder="S3 region" value="{{ @$disk_config->region }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="bucket" class="col-md-12 col-form-label text-md-right">Bucket</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="bucket" id="bucket" class="form-control" placeholder="S3 bucket" value="{{ @$disk_config->bucket }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="endpoint" class="col-md-12 col-form-label text-md-right">Endpoint</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="endpoint" id="endpoint" class="form-control" placeholder="S3 endpoint" value="{{ @$disk_config->endpoint }}" />
                    </div>
                   </div>

				  </div>
				  <div id="google_drive_details" style="display:none;">

                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="client_id" class="col-md-12 col-form-label text-md-right">Client ID</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="client_id" id="client_id" class="form-control" placeholder="Client ID" value="{{ @$disk_config->clientId }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="client_secret" class="col-md-12 col-form-label text-md-right">Client Secret</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="client_secret" id="client_secret" class="form-control" placeholder="Client secret" value="{{ @$disk_config->clientSecret }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="refresh_token" class="col-md-12 col-form-label text-md-right">Refresh Token</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="refresh_token" id="refresh_token" class="form-control" placeholder="Refresh Token" value="{{ @$disk_config->refreshToken }}" />
                    </div>
                   </div>
                   <div class="form-group row">
                   <div class="col-md-3">
                   <label for="folder_id" class="col-md-12 col-form-label text-md-right">Folder ID</label> 
                    </div>
                    <div class="col-md-9">
                    <input type="text" name="folder_id" id="folder_id" class="form-control" placeholder="Folder ID" value="{{ @$disk_config->folderId }}" />
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
