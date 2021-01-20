@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
@push('js')
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js" defer></script>
<script type="text/javascript" src="/js/transliteration-input.bundle.js"></script>
<link href="/css/jquery-ui.css" rel="stylesheet">
<script>
var deldialog;
$(document).ready(function() {
    oTable = $('#documents').DataTable({
    "aoColumnDefs": [
           { "bSortable": false, "aTargets": [4]},
           { "className": 'text-right dt-nowrap', "aTargets": [2,3]},
           { "className": 'td-actions text-right dt-nowrap', "aTargets": [4]}
     ],
    "processing":true,
    "order": [[ 3, "desc" ]],
    "serverSide":true,
    "ajax":'/collection/{{$collection->id}}/search',
    "language": 
	{          
	"processing": "<img src='/i/processing.gif'>",
	},
    "columns":[
       {data:"type",
          render:{
            '_':'display',
            'sort':'filetype'
          }
       },
       {data:"title"},
       {data:"size",
           render:{
             '_': 'display',
             'sort': 'bytes'
            }
        },
        {data:"updated_at",
            render:{
               '_':'display',
              'sort': 'updated_date'
            }
        },
        {data:"actions"},
    ],
    });

} );

function showDeleteDialog(document_id){
	str = randomString(6);
	$('#text_captcha').text(str);
	$('#hidden_captcha').text(str);
	$('#delete_doc_id').val(document_id);
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
@endpush
	    <div id="deletedialog" style="display:none;">
		<form name="deletedoc" method="post" action="/document/delete">
		@csrf
		<p>Enter <span id="text_captcha"></span> to delete</p>
		<input type="text" name="delete_captcha" value="" />
		<input type="hidden" id="hidden_captcha" name="hidden_captcha" value="" />
		<input type="hidden" id="delete_doc_id" name="document_id" value="" />
		<button class="btn btn-danger" type="submit" value="delete">Delete</button>
		</form>
	    </div>
<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
		<div class="card-header card-header-primary">
                <h4 class="card-title ">
            	<a href="/collections">Collections</a> :: {{ $collection->name }}
		</h4>
            </div>
        <div class="card-body">
		<div class="row">
                  <div class="col-12 text-right">
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'MAINTAINER'))
                    <a title="Manage Users of this collection" href="/collection/{{ $collection->id }}/users" class="btn btn-sm btn-primary"><i class="material-icons">people</i></a>
		    @if($collection->content_type == 'Uploaded documents')	
                    <a title="Manage meta information fields of this collection" href="/collection/{{ $collection->id }}/meta" class="btn btn-sm btn-primary"><i class="material-icons">label</i></a>
		    @elseif($collection->content_type == 'Web resources')	
                    <a title="Manage Sites for this collection" href="/collection/{{ $collection->id }}/save_exclude_sites" class="btn btn-sm btn-primary"><i class="material-icons">insert_link</i></a>
		    @endif
		  @endif
                  @if(Auth::user() && Auth::user()->hasPermission($collection->id, 'CREATE') && $collection->content_type == 'Uploaded documents')
                    <a title="New Document" href="/collection/{{ $collection->id }}/upload" class="btn btn-sm btn-primary"><i class="material-icons">add</i></a>
		  @endif
                  @if(count($collection->meta_fields)>0)
                    <a href="/collection/{{ $collection->id }}/metafilters" title="Set Filters" class="btn btn-sm btn-primary"><i class="material-icons">filter_list</i></a>
                  @endif
                  </div>
        </div>
            <p>{{ $collection->description }}</p>
        <p>
        @php
            $meta_fields = $collection->meta_fields;
            $meta_labels = array();
            foreach($meta_fields as $m){
                $meta_labels[$m->id] = $m->label;
            }
            $all_meta_filters = Session::get('meta_filters');
        @endphp
        @if(count($meta_fields)>0 && !empty($all_meta_filters[$collection->id]))
            <strong>Current Filters:</strong>
        @foreach( $all_meta_filters[$collection->id] as $m)
            <span class="filtertag">
            {{ $meta_labels[$m['field_id']] }} {{ $m['operator'] }} <i>{{ $m['value'] }}</i>
                <a class="removefiltertag" title="remove" href="/collection/{{ $collection->id }}/removefilter/{{ $m['filter_id'] }}">
                <i class="tinyicon material-icons">delete</i>
                </a>
                </span>
        @endforeach
        @endif
        </p>

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
		@if(!empty(env('TRANSLITERATION')))
		   <div class="col-12 text-right">
		   <input type="text" id="collection_search" placeholder="Search" />
			<style>
			.dataTables_filter {
			display: none;
			}
			</style>
		   </div>
		<script>
			let searchbox = document.getElementById("collection_search");
			enableTransliteration(searchbox, '{{ env('TRANSLITERATION') }}');
			$('#collection_search').keyup(function(){
      			oTable.search($(this).val()).draw() ;
			})
		</script>
		@endif
		   <div class="table-responsive">
                    <table id="documents" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th class="text-right"><!--Actions--></th>
                            </tr>
                        </thead>
                    </table>
		    </div>
                 </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
