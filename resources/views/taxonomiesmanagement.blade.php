@extends('layouts.app',['class'=> 'off-canvas-sidebar', 'title'=>'Smart Repository'])

@section('content')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<link href="/css/jquery-ui.css" rel="stylesheet">

<script type="text/javascript">
$(document).ready(function() {
    $("#taxonomy").DataTable();
} );

function showDeleteDialog(taxonomy_id){
        str = randomString(6);
        $('#text_captcha').text(str);
        $('#hidden_captcha').text(str);
        $('#delete_taxonomy_id').val(taxonomy_id);
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

<script>
var toggler = document.getElementsByClassName("caret");
var i;

for (i = 0; i < toggler.length; i++) {
  toggler[i].addEventListener("click", function() {
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("caret-down");
  });
}
</script>


<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
	<!--
            <div class="card">
                <div class="card-header card-header-primary">Add Taxonomies</div>
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
                </div>
            </div>
	-->
            <div class="card">
                <div class="card-header card-header-primary">
		<h4 class="card-title ">{{ __('Taxonomies') }}</h4>
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
                    <a href="{{ route('taxonomies.create') }}" class="btn btn-sm btn-primary" title="Add Taxonomies"><i class="material-icons">add</i></a>
                  </div>
                </div>


  
		<div class="table-responsive">
		    <h3>Taxonomies List</h3>
            @foreach ($taxonomies as $u)
            <ul id="myUL">
                 <li><span class="caret">{{$u->label}}</span>
                 <a href="/taxonomies/{{ $u->id }}/edit" rel="tooltip" class="btn btn-success btn-link">
                <i class="material-icons">edit</i>
                    <div class="ripple-container"></div>
                    </a>
                 <span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog({{ $u->id }});" title="Delete Taxonomies"><i class="material-icons">delete</i></span>
                     <ul class="nested">
                     <li><span class="caret">{{$u->label}}
                     <a href="/taxonomies/{{ $u->id }}/edit" rel="tooltip" class="btn btn-success btn-link">
                    <i class="material-icons">edit</i>
                    <div class="ripple-container"></div>
                    </a>
                 <span class="btn btn-danger btn-link confirmdelete" onclick="showDeleteDialog({{ $u->id }});" title="Delete Taxonomies"><i class="material-icons">delete</i></span>
                     </li>
                     </ul>
                 </li>
                 </ul>
                 <div id="deletedialog" style="display:none;">
                <form name="deletedoc" method="post" action="/admin/taxonomies/delete">
                @csrf
                <p>Enter <span id="text_captcha"></span> to delete</p>
                <input type="text" name="delete_captcha" value="" />
                <input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
                <input type="hidden" id="delete_taxonomy_id" name="taxonomy_id" value="{{ $u->id }}" />
                <button class="btn btn-danger" type="submit" value="delete">Delete</button>
                </form>
                </div>
                 @endforeach
                </div>

                </div>
            </div>
        </div>
    </div>
    </div>
</div>


<script>
var toggler = document.getElementsByClassName("caret");
var i;

for (i = 0; i < toggler.length; i++) {
  toggler[i].addEventListener("click", function() {
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("caret-down");
  });
}
</script>
@endsection

