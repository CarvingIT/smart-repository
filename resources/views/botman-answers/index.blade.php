@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'title'=>'Smart Repository'])

@section('content')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">

<script type="text/javascript">
$(document).ready(function() {
    $("#records").DataTable();
} );

function showDeleteDialog(record_id){
		// set form action
		$('#deleterecord').attr('action', '/botman-answers/'+record_id);
        str = randomString(6);
        $('#text_captcha').text(str);
        $('#hidden_captcha').val(str);
        $('#delete_record_id').val(record_id);
        deldialog = $( "#deletedialog" ).dialog({
                title: 'Are you sure ?',
                resizable: true
        });
}

function randomString(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}


</script>



<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
		<h4 class="card-title ">{{ __('Manage chatbot answers') }}</h4>
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
                <div class="row">
                  <div class="col-12 text-right">
                    <a href="{{ route('botman-answers.create') }}" class="btn btn-sm btn-primary" title="Add Question"><i class="material-icons">add</i></a>
                  </div>
                </div>

		<div class="table-responsive">
                    <table id="records" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>Questions</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($records as $r)
                        <tr>
                            <td>{{ $r->question }}</td>
                            <td class="td-actions text-right">
                            <a href="/admin/botman-answers/{{ $r->id }}/edit" rel="tooltip" class="btn btn-success btn-link">
				    <i class="material-icons">edit</i>
                                    <div class="ripple-container"></div>
				</a>
				<span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog({{ $r->id }});" title="Delete Record"><i class="material-icons">delete</i></span>
                            </td>
                        </tr>
	    <div id="deletedialog" style="display:none;">
                <form id="deleterecord" name="deleterecord" method="post" action="/botman-answers/">
                @csrf
				@method('DELETE')
                <p>Enter <span id="text_captcha"></span> to delete</p>
                <input type="text" name="delete_captcha" value="" />
                <input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
                <input type="hidden" id="delete_record_id" name="record_id" value="{{ $r->id }}" />
                <button class="btn btn-danger" type="submit" value="delete">Delete</button>
                </form>
            </div>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
