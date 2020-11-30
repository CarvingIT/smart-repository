@extends('layouts.app',['class'=> 'off-canvas-sidebar','title'=>'Smart Repository'])

@section('content')
<!--
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
-->
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">

<script>
$(document).ready(function() {
    $('#collections').DataTable();
} );

function showDeleteDialog(collection_id){
        str = randomString(6);
        $('#text_captcha').text(str);
        $('#hidden_captcha').text(str);
        $('#delete_collection_id').val(collection_id);
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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Collections</h4>
                    <!--div class="card-header-corner" style="margin-top:-4%;"><a href="/admin/collection-form/new"><img class="icon" src="/i/plus.png"/></a></div-->
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
                    <a href="/admin/collection-form/new" class="btn btn-sm btn-primary" title="Add Collection"><i class="material-icons">add</i></a>
                  </div>
                </div>

			<div class="table-responsive">
                    <table id="collections" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($collections as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->type }}</td>
                            <td>{{ $c->created_at }}</td>
                            <td class="td-actions text-right">
                                <a rel="tooltip" class="btn btn-success btn-link" href="/admin/collection-form/{{$c->id}}">
				<i class="material-icons">edit</i>
                                <div class="ripple-container"></div>
				</a>
                                <!--a rel="tooltip" class="btn btn-danger btn-link" href="/admin/collection-form/{{$c->id}}/delete">
				<i class="material-icons">delete</i>
                                <div class="ripple-container"></div>
				</a-->
			<span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog({{ $c->id }});" title="Delete Collection"><i class="material-icons">delete</i></span>
                            </td>    <!-- use font awesome icons or image icons -->
                        </tr>
            <div id="deletedialog" style="display:none;">
                <form name="deletedoc" method="post" action="/admin/collection-form/delete">
                @csrf
                <p>Enter <span id="text_captcha"></span> to delete</p>
                <input type="text" name="delete_captcha" value="" />
                <input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
                <input type="hidden" id="delete_collection_id" name="collection_id" value="{{ $c->id }}" />
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
